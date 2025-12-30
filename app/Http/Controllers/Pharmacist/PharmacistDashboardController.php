<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Auth;

class PharmacistDashboardController extends Controller
{
    /**
     * Récupère la pharmacie du pharmacien connecté
     * - users.pharmacy_id si présent
     * - sinon la première via pivot users <-> pharmacies
     */
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

    public function index()
{
    $user     = Auth::user();
    $pharmacy = $this->currentPharmacy();
    $pharmacyId = $pharmacy?->id;

    // Si aucun rattachement → pas de commandes
    if (!$pharmacyId) {
        $pendingCount   = 0;
        $validatedCount = 0;
        $rejectedCount  = 0;
        $recentOrders   = collect();
    } else {
        $pendingCount = CustomerOrder::query()
            ->where('status', 'en_attente')
            ->where('pharmacy_id', $pharmacyId)
            ->count();

        $validatedCount = CustomerOrder::query()
            ->where('status', 'valide')
            ->where('pharmacy_id', $pharmacyId)
            ->count();

        $rejectedCount = CustomerOrder::query()
            ->where('status', 'rejete')
            ->where('pharmacy_id', $pharmacyId)
            ->count();

        $recentOrders = CustomerOrder::with('user')
            ->where('pharmacy_id', $pharmacyId)
            ->latest()
            ->limit(5)
            ->get();
    }

    return view('pharmacist.dashboard', [
        'user'           => $user,
        'pharmacy'       => $pharmacy,
        'pendingCount'   => $pendingCount,
        'validatedCount' => $validatedCount,
        'rejectedCount'  => $rejectedCount,
        'recentOrders'   => $recentOrders,
    ]);
}

    public function manage()
    {
        $user     = Auth::user();
        $pharmacy = $this->currentPharmacy();

        return view('pharmacist.manage', [
            'user'     => $user,
            'pharmacy' => $pharmacy,
        ]);
    }
}