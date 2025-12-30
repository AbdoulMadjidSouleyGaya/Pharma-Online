<?php

namespace App\Http\Controllers;

use App\Models\PharmaProduct;
use Illuminate\Http\Request;
use App\Services\AbdouPharmaImporter;

class PharmacistApiController extends Controller
{
    /**
     * Page de gestion API côté pharmacien.
     * Affiche l’état de la connexion + résumé de la sync.
     */
    public function show(Request $request)
    {
        $user     = $request->user();
        $pharmacy = $user->pharmacy; // relation user->pharmacy (à garder)

        if (! $pharmacy) {
            return redirect()
                ->route('pharmacist.dashboard')
                ->with('hint', 'Aucune pharmacie associée à ce compte pharmacien.');
        }

        // Petit résumé local (statistiques de sync)
        $productsCount = PharmaProduct::where('pharmacy_id', $pharmacy->id)->count();
        $lastSyncAt    = PharmaProduct::where('pharmacy_id', $pharmacy->id)->max('synced_at');

        // Placeholder pour enrichir plus tard (logs, dernier statut API, etc.)
        $apiStatus       = null;
        $lastSyncSummary = null;
        $logs            = [];

        return view('pharmacist.api', compact(
            'pharmacy',
            'apiStatus',
            'lastSyncAt',
            'lastSyncSummary',
            'productsCount',
            'logs'
        ));
    }

    /**
     * Tester la connexion à l’API AbdouPharma.
     */
    public function test(Request $request, AbdouPharmaImporter $importer)
    {
        $user     = $request->user();
        $pharmacy = $user->pharmacy;

        if (! $pharmacy) {
            return redirect()->route('pharmacist.api')
                ->with('hint', 'Aucune pharmacie associée à ce compte pharmacien.');
        }

        if (! $pharmacy->api_token) {
            return redirect()->route('pharmacist.api')
                ->with('hint', 'Aucun token configuré pour votre pharmacie. Merci de contacter l’administrateur.');
        }

        try {
            $result = $importer->testConnection($pharmacy);

            if ($result['ok']) {
                $count = $result['products_count'] ?? 0;

                return redirect()->route('pharmacist.api')
                    ->with('ok', "Connexion à AbdouPharma ✅ ({$count} produit(s) retourné(s)).");
            }

            return redirect()->route('pharmacist.api')
                ->with('hint', $result['message'] . ' (' . ($result['status'] ?? 'N/A') . ')');

        } catch (\Throwable $e) {
            return redirect()->route('pharmacist.api')
                ->with('hint', 'Erreur lors du test de connexion : ' . $e->getMessage());
        }
    }

    /**
     * Synchroniser les produits depuis AbdouPharma.
     */
    public function load(Request $request, AbdouPharmaImporter $importer)
    {
        $user     = $request->user();
        $pharmacy = $user->pharmacy;

        if (! $pharmacy) {
            return redirect()->route('pharmacist.api')
                ->with('hint', 'Aucune pharmacie associée à ce compte pharmacien.');
        }

        if (! $pharmacy->api_token) {
            return redirect()->route('pharmacist.api')
                ->with('hint', 'Aucun token configuré. Merci de contacter l’administrateur pour renseigner le token AbdouPharma.');
        }

        try {
            $result = $importer->importAll($pharmacy);

            return redirect()
                ->route('pharmacist.api')
                ->with('ok', "Synchronisation lancée ✅ ({$result['imported']} produit(s) récupéré(s) depuis AbdouPharma).");
        } catch (\Throwable $e) {
            return redirect()
                ->route('pharmacist.api')
                ->with('hint', 'Erreur lors de la synchro : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les produits synchronisés localement pour la pharmacie.
     */
    public function products(Request $request)
    {
        $user     = $request->user();
        $pharmacy = $user->pharmacy;

        if (! $pharmacy) {
            return redirect()->route('pharmacist.api')
                ->with('hint', 'Aucune pharmacie associée.');
        }

        $products = PharmaProduct::where('pharmacy_id', $pharmacy->id)
            ->orderByDesc('synced_at')
            ->paginate(10);

        return view('pharmacist.api-products', compact('pharmacy', 'products'));
    }
}
