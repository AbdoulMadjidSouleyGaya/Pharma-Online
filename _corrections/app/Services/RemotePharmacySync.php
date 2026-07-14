<?php

namespace App\Services;

use App\Models\Pharmacy;
use App\Models\PharmaProduct;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RemotePharmacySync
{
    /**
     * URL de base de l'API AbdouPharma (instance CENTRALE multi-tenant).
     * Exemple : http://127.0.0.1:9000
     */
    protected string $baseUrl;

    public function __construct()
    {
        // Une seule instance AbdouPharma (multi-tenant).
        // La sélection de la base se fait côté AbdouPharma via le token de la pharmacie.
        $this->baseUrl = rtrim(config('services.abdoupharma.base'), '/');
    }

    /**
     * Synchronise les produits AbdouPharma vers la table locale pharma_products
     * pour une pharmacie donnée.
     *
     * On récupère les produits via l'API AbdouPharma, puis on fait un
     * updateOrCreate sur PharmaProduct, en incluant bien le supplier_name.
     */
    public function syncProductsForPharmacy(Pharmacy $pharmacy): void
    {
        $token = $pharmacy->api_token ?: config('services.abdoupharma.token');

        if (empty($this->baseUrl) || empty($token)) {
            Log::warning('⚠️ Sync produits : configuration AbdouPharma incomplète.', [
                'pharmacy_id'   => $pharmacy->id ?? null,
                'pharmacy_name' => $pharmacy->name ?? null,
                'base_url'      => $this->baseUrl,
                'token_present' => !empty($token),
            ]);
            return;
        }

        try {
            $url = "{$this->baseUrl}/api/v1/products";

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($url);

            if ($response->failed()) {
                Log::error('❌ Échec de la récupération des produits AbdouPharma', [
                    'pharmacy_id'   => $pharmacy->id ?? null,
                    'pharmacy_name' => $pharmacy->name ?? null,
                    'status'        => $response->status(),
                    'body'          => $response->body(),
                    'url'           => $url,
                ]);
                return;
            }

            $payload = $response->json();

            // Selon la forme de la réponse ({ data: [...] } ou liste directe)
            $products = $payload['data'] ?? $payload ?? [];

            if (!is_array($products)) {
                Log::error('❌ Format JSON inattendu pour la liste de produits AbdouPharma', [
                    'pharmacy_id'   => $pharmacy->id ?? null,
                    'pharmacy_name' => $pharmacy->name ?? null,
                    'payload'       => $payload,
                ]);
                return;
            }

            $count = 0;

            foreach ($products as $remote) {
                if (!is_array($remote) || !isset($remote['id'])) {
                    continue;
                }

                PharmaProduct::updateOrCreate(
                    [
                        'pharmacy_id' => $pharmacy->id,
                        'remote_id'   => $remote['id'],
                    ],
                    [
                        'libelle'        => $remote['libelle']        ?? null,
                        'supplier_name'  => $remote['supplier_name']  ?? null, // 🆕 Fournisseur texte
                        'prix'           => $remote['prix']           ?? 0,
                        'stock'          => $remote['stock']          ?? 'Disponible',
                        'quantity'       => $remote['quantity']       ?? 0,
                        'description'    => $remote['description']    ?? null,
                        'image'          => $remote['image']          ?? null,
                        'synced_at'      => now(),
                    ]
                );

                $count++;
            }

            Log::info('✅ Sync produits AbdouPharma terminée', [
                'pharmacy_id'   => $pharmacy->id ?? null,
                'pharmacy_name' => $pharmacy->name ?? null,
                'count'         => $count,
            ]);
        } catch (\Throwable $e) {
            Log::error('🔥 Exception pendant la synchronisation des produits AbdouPharma', [
                'pharmacy_id'   => $pharmacy->id ?? null,
                'pharmacy_name' => $pharmacy->name ?? null,
                'message'       => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Décrémente un produit dans AbdouPharma pour une pharmacie donnée.
     *
     * - $pharmacy->api_token = token Sanctum émis par AbdouPharma
     * - côté AbdouPharma, le middleware UsePharmacyDatabase utilise ce token
     *   pour activer la bonne base de données "tenant".
     *
     * ⚠️ CORRECTIF (voir README.md du dossier _corrections) :
     * L'ancienne version faisait 2 appels HTTP séparés :
     *   1) GET  /api/v1/products/{id}          → lire la quantité actuelle
     *   2) PUT  /api/v1/products/{id}          → réécrire quantity/stock calculés ici, en PHP
     * Entre ces deux appels, une autre commande cliente simultanée pouvait lire
     * la même quantité de départ : les deux commandes décrémentaient alors à
     * partir de la même valeur, et l'une des deux décrémentations était perdue
     * (risque réel de survente en cas de forte activité).
     *
     * La nouvelle version appelle le endpoint dédié et désormais exposé côté
     * AbdouPharma : POST /api/v1/products/{id}/decrement, qui fait tout
     * (lecture + calcul + écriture) en une seule requête HTTP, elle-même
     * exécutée dans une transaction avec verrouillage de ligne côté AbdouPharma.
     * Un seul appel HTTP au lieu de deux, et plus aucune fenêtre de "race
     * condition" côté PharmaOnline.
     *
     * @param  Pharmacy    $pharmacy   Pharmacie locale concernée
     * @param  int|string  $remoteId   ID du produit côté AbdouPharma
     * @param  int         $orderedQty Quantité commandée
     * @return bool
     */
    public function decrementProduct(Pharmacy $pharmacy, int|string $remoteId, int $orderedQty): bool
    {
        $token = $pharmacy->api_token;

        if (empty($token)) {
            Log::warning('⚠️ Aucun token AbdouPharma configuré pour cette pharmacie lors de la décrémentation.', [
                'pharmacy_id'   => $pharmacy->id ?? null,
                'pharmacy_name' => $pharmacy->name ?? null,
                'remote_id'     => $remoteId,
                'ordered_qty'   => $orderedQty,
            ]);

            // On ne jette pas d'exception pour ne pas casser la validation côté PharmaOnline.
            return false;
        }

        $url = "{$this->baseUrl}/api/v1/products/{$remoteId}/decrement";

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->post($url, [
                    'quantity' => $orderedQty,
                ]);

            if ($response->failed()) {
                Log::error("❌ Échec de la décrémentation du produit #{$remoteId} sur AbdouPharma", [
                    'pharmacy_id'   => $pharmacy->id ?? null,
                    'pharmacy_name' => $pharmacy->name ?? null,
                    'status'        => $response->status(),
                    'body'          => $response->body(),
                    'url'           => $url,
                    'ordered_qty'   => $orderedQty,
                ]);

                return false;
            }

            $body = $response->json();

            Log::info('🌍 Synchro OK vers AbdouPharma (décrémentation atomique)', [
                'pharmacy_id'    => $pharmacy->id ?? null,
                'pharmacy_name'  => $pharmacy->name ?? null,
                'remote_id'      => $remoteId,
                'ordered_qty'    => $orderedQty,
                'nouvelle_qte'   => $body['quantity'] ?? null,
                'statut'         => $body['stock'] ?? null,
            ]);

            return true;

        } catch (\Throwable $e) {
            Log::error('⚠️ Erreur lors de la synchro AbdouPharma (décrémentation)', [
                'pharmacy_id'   => $pharmacy->id ?? null,
                'pharmacy_name' => $pharmacy->name ?? null,
                'id'            => $remoteId,
                'ordered_qty'   => $orderedQty,
                'url'           => $url,
                'message'       => $e->getMessage(),
            ]);

            return false;
        }
    }
}
