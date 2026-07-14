<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PharmacistReportController extends Controller
{
    /** Récupère la pharmacie du pharmacien connecté */
    protected function currentPharmacy(): ?Pharmacy
    {
        $user = Auth::user();
        if (!$user) return null;

        if (!empty($user->pharmacy_id)) {
            return Pharmacy::find($user->pharmacy_id);
        }
        if (method_exists($user, 'pharmacies')) {
            return $user->pharmacies()->first();
        }
        return null;
    }

    public function index(Request $request)
    {
        $pharmacy = $this->currentPharmacy();
        abort_unless($pharmacy, 403, "Aucune pharmacie rattachée.");

        // period = 7, 30, 90 ou "all"
        $period = $request->query('period', '30');
        $days   = is_numeric($period) ? (int) $period : null;

        $baseQuery = CustomerOrder::where('pharmacy_id', $pharmacy->id);

        // 🔹 Si "all" → pas de filtre date
        // 🔹 Sinon filtre sur les N derniers jours
        if (!is_null($days) && $days > 0) {
            $from = now()->subDays($days);
            $baseQuery->whereDate('created_at', '>=', $from->toDateString());
        }

        // ============================
        // Normalisation des statuts
        // ============================
        $statusPending   = ['en_attente', 'en_cours', 'pending'];
        $statusValidated = ['valide', 'validated'];
        $statusRejected  = ['rejete', 'rejected', 'annulee', 'cancelled'];

        // ======================
        // STATISTIQUES GLOBALES
        // ======================
        $totalOrders = (clone $baseQuery)->count();
        $validated   = (clone $baseQuery)->whereIn('status', $statusValidated)->count();
        $rejected    = (clone $baseQuery)->whereIn('status', $statusRejected)->count();
        $pending     = (clone $baseQuery)->whereIn('status', $statusPending)->count();

        // Montant total (si tu veux seulement validées, remplace par whereIn(statusValidated))
       $totalAmount = (clone $baseQuery)
    ->whereIn('status', array_merge($statusValidated, $statusPending))
    ->sum('total');

         $avgAmount   = $totalOrders > 0 ? $totalAmount / $totalOrders : 0;

        $effectivePeriod = $days ?? 'all';

        $stats = [
            'period'       => (string) $effectivePeriod,
            'period_label' => $effectivePeriod === 'all'
                ? "toute l’historique"
                : "les {$effectivePeriod} derniers jours",
            'total_orders' => (int) $totalOrders,
            'validated'    => (int) $validated,
            'rejected'     => (int) $rejected,
            'pending'      => (int) $pending,
            'total_amount' => (int) $totalAmount,
            'avg_amount'   => (float) $avgAmount,
        ];

        Log::info('📊 Pharmacist reports', [
            'pharmacy_id'  => $pharmacy->id,
            'period'       => $stats['period'],
            'total_orders' => $stats['total_orders'],
        ]);

        // ======================
        // TOP PRODUITS (items JSON)
        // ======================
        $topProductsArray = [];

        (clone $baseQuery)
            ->whereNotNull('items')
            ->orderBy('created_at', 'desc')
            ->chunk(100, function ($orders) use (&$topProductsArray) {
                foreach ($orders as $order) {
                    $items = (array) ($order->items ?? []);

                    foreach ($items as $line) {
                        if (!is_array($line)) continue;

                        $id    = $line['id'] ?? $line['product_id'] ?? null;
                        $qty   = (int)($line['qty'] ?? $line['quantity'] ?? 1);
                        $price = (int)($line['price'] ?? 0);

                        if (!$id) continue;

                        $key = (string) $id;

                        if (!isset($topProductsArray[$key])) {
                            $topProductsArray[$key] = [
                                'id'      => $id,
                                'libelle' => $line['name'] ?? $line['libelle'] ?? ('Produit #'.$id),
                                'qty'     => 0,
                                'amount'  => 0,
                            ];
                        }

                        $topProductsArray[$key]['qty']    += $qty;
                        $topProductsArray[$key]['amount'] += $price * $qty;
                    }
                }
            });

        $topProducts = collect($topProductsArray)
            ->sortByDesc('qty')
            ->take(10)
            ->values()
            ->all();

        return view('pharmacist.reports', [
            'pharmacy'    => $pharmacy,
            'stats'       => $stats,
            'topProducts' => $topProducts,
        ]);
    }
}
