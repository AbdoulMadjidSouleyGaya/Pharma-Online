<?php

namespace App\Services;

use App\Models\PharmaProduct;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AbdouPharmaImporter
{
    protected string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = rtrim($baseUrl ?: (string) config('services.abdoupharma.base', ''), '/');
    }

    /**
     * Teste la connexion à l'API AbdouPharma pour une pharmacie donnée.
     *
     * @return array{ok:bool,status:int|null,message:string,products_count:int|null}
     */
    public function testConnection(Pharmacy $pharmacy): array
    {
        $token = $pharmacy->api_token;

        if (! $token) {
            throw new RuntimeException('Aucun token API configuré pour cette pharmacie.');
        }

        $url = $this->baseUrl . '/api/v1/products';

        try {
            $resp = Http::withToken($token)
                ->acceptJson()
                ->timeout(8)
                ->get($url);

            if ($resp->successful()) {
                $json  = $resp->json();
                $count = is_array($json)
                    ? (is_array($json['data'] ?? null) ? count($json['data']) : count($json))
                    : 0;

                Log::info('abdoupharma.test.ok', [
                    'pharmacy_id'   => $pharmacy->id,
                    'pharmacy_name' => $pharmacy->name,
                    'status'        => $resp->status(),
                    'products'      => $count,
                    'url'           => $url,
                ]);

                return [
                    'ok'             => true,
                    'status'         => $resp->status(),
                    'message'        => 'Connexion à AbdouPharma réussie.',
                    'products_count' => $count,
                ];
            }

            $body    = $resp->json() ?: [];
            $message = $body['message'] ?? 'API joignable mais réponse inattendue';
            $error   = $body['error']   ?? null;

            if ($error) {
                $message .= ' → ' . $error;
            }

            Log::warning('abdoupharma.test.failed', [
                'pharmacy_id'   => $pharmacy->id,
                'pharmacy_name' => $pharmacy->name,
                'status'        => $resp->status(),
                'message'       => $message,
                'url'           => $url,
                'body'          => $body,
            ]);

            return [
                'ok'             => false,
                'status'         => $resp->status(),
                'message'        => $message,
                'products_count' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('abdoupharma.test.error', [
                'pharmacy_id'   => $pharmacy->id,
                'pharmacy_name' => $pharmacy->name,
                'url'           => $url,
                'exception'     => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Importe tous les produits AbdouPharma pour une pharmacie donnée.
     *
     * @return array{imported:int,pages:int}
     */
    public function importAll(Pharmacy $pharmacy, int $perPage = 100): array
    {
        $token = $pharmacy->api_token;

        if (! $token) {
            throw new RuntimeException('Aucun token API configuré pour cette pharmacie.');
        }

        $baseUrl    = $this->baseUrl . '/api/v1/products';
        $page       = 1;
        $imported   = 0;
        $pages      = 0;
        $pharmacyId = $pharmacy->id;

        Log::info('abdoupharma.sync.start', [
            'pharmacy_id'   => $pharmacyId,
            'pharmacy_name' => $pharmacy->name,
            'base_url'      => $baseUrl,
            'per_page'      => $perPage,
        ]);

        try {
            do {
                $resp = Http::withToken($token)
                    ->acceptJson()
                    ->get($baseUrl, [
                        'page'     => $page,
                        'per_page' => $perPage,
                    ]);

                if (! $resp->successful()) {
                    Log::warning('abdoupharma.sync.page_failed', [
                        'pharmacy_id'   => $pharmacyId,
                        'pharmacy_name' => $pharmacy->name,
                        'status'        => $resp->status(),
                        'page'          => $page,
                        'base_url'      => $baseUrl,
                        'body'          => $resp->json(),
                    ]);
                    break;
                }

                $json = $resp->json();
                $data = $json['data'] ?? $json ?? [];
                $next = $json['links']['next'] ?? null;

                Log::info('abdoupharma.sync.page_ok', [
                    'pharmacy_id'   => $pharmacyId,
                    'pharmacy_name' => $pharmacy->name,
                    'page'          => $page,
                    'received'      => is_countable($data) ? count($data) : 0,
                ]);

                foreach ($data as $item) {
                    $remoteId = $item['id'] ?? $item['remote_id'] ?? null;
                    $libelle  = $item['libelle'] ?? $item['name'] ?? null;

                    if (! $remoteId || ! $libelle) {
                        Log::warning('abdoupharma.sync.skip_item', [
                            'pharmacy_id' => $pharmacyId,
                            'item'        => $item,
                        ]);
                        continue;
                    }

                    $prix   = isset($item['prix']) ? (float) $item['prix'] : (float) ($item['price'] ?? 0);
                    $qty    = (int) ($item['quantity'] ?? $item['stock'] ?? 0);
                    $stock  = $item['stock'] ?? null;
                    $stockTxt = is_string($stock) && $stock !== ''
                        ? $stock
                        : ($qty > 0 ? 'Disponible' : 'Rupture');

                    $local = PharmaProduct::where('remote_id', $remoteId)
                        ->where('pharmacy_id', $pharmacyId)
                        ->first();

                    if ($local) {
                        $local->update([
                            'libelle'    => $libelle,
                            'prix'       => $prix,
                            'quantity'   => $qty,
                            'stock'      => $stockTxt,
                            'synced_at'  => now(),
                        ]);

                        Log::info('abdoupharma.sync.update_product', [
                            'pharmacy_id' => $pharmacyId,
                            'remote_id'   => $remoteId,
                            'local_id'    => $local->id,
                            'libelle'     => $libelle,
                            'quantity'    => $qty,
                        ]);
                    } else {
                        $created = PharmaProduct::create([
                            'remote_id'   => $remoteId,
                            'libelle'     => $libelle,
                            'prix'        => $prix,
                            'quantity'    => $qty,
                            'stock'       => $stockTxt,
                            'pharmacy_id' => $pharmacyId,
                            'synced_at'   => now(),
                        ]);

                        Log::info('abdoupharma.sync.create_product', [
                            'pharmacy_id' => $pharmacyId,
                            'remote_id'   => $remoteId,
                            'local_id'    => $created->id,
                            'libelle'     => $libelle,
                            'quantity'    => $qty,
                        ]);
                    }

                    $imported++;
                }

                $pages++;
                $page++;

            } while (! empty($data) && $next);

            Log::info('abdoupharma.sync.completed', [
                'pharmacy_id'   => $pharmacyId,
                'pharmacy_name' => $pharmacy->name,
                'imported'      => $imported,
                'pages'         => $pages,
            ]);

        } catch (\Throwable $e) {
            Log::error('abdoupharma.sync.error', [
                'pharmacy_id'   => $pharmacyId,
                'pharmacy_name' => $pharmacy->name,
                'page'          => $page,
                'imported'      => $imported,
                'exception'     => $e->getMessage(),
            ]);

            throw $e;
        }

        return [
            'imported' => $imported,
            'pages'    => $pages,
        ];
    }
}
