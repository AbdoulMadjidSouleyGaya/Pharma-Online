<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

        // Filtre période
        $to   = $request->filled('to')   ? Carbon::parse($request->get('to'))->endOfDay()   : now()->endOfDay();
        $from = $request->filled('from') ? Carbon::parse($request->get('from'))->startOfDay() : now()->subDays(29)->startOfDay();

        // Base
        $base = CustomerOrder::where('pharmacy_id', $pharmacy->id)
            ->whereBetween('created_at', [$from, $to]);

        // Agrégats
        $validatedQ = (clone $base)->where('status', 'valide');
        $rejectedQ  = (clone $base)->where('status', 'rejete');

        $validatedCount = (clone $validatedQ)->count();
        $rejectedCount  = (clone $rejectedQ)->count();

        $validatedTotal = (clone $validatedQ)->sum('total');
        $avgBasket      = $validatedCount > 0 ? round($validatedTotal / $validatedCount) : 0;

        // Par jour (labels + 2 séries)
        $perDay = (clone $base)
            ->select([
                DB::raw("DATE(created_at) as d"),
                DB::raw("SUM(CASE WHEN status='valide' THEN 1 ELSE 0 END) as v_count"),
                DB::raw("SUM(CASE WHEN status='rejete' THEN 1 ELSE 0 END) as r_count"),
                DB::raw("SUM(CASE WHEN status='valide' THEN total ELSE 0 END) as v_total"),
            ])
            ->groupBy('d')
            ->orderBy('d','asc')
            ->get();

        // Normaliser la plage (assurer 0 les jours manquants)
        $cursor = $from->copy();
        $labels = [];
        $seriesValidated = [];
        $seriesRejected  = [];
        $seriesValTotal  = [];

        $map = $perDay->keyBy('d');
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $row = $map->get($key);
            $labels[]         = $cursor->format('d/m');
            $seriesValidated[] = $row ? (int)$row->v_count : 0;
            $seriesRejected[]  = $row ? (int)$row->r_count : 0;
            $seriesValTotal[]  = $row ? (int)$row->v_total : 0;
            $cursor->addDay();
        }

        // Dernières 15 commandes (tous statuts) pour contexte
        $latest = CustomerOrder::with('user')
            ->where('pharmacy_id', $pharmacy->id)
            ->orderBy('created_at','desc')
            ->limit(15)
            ->get();

        return view('pharmacist.reports', [
            'pharmacy'         => $pharmacy,
            'from'             => $from,
            'to'               => $to,
            'validatedCount'   => $validatedCount,
            'rejectedCount'    => $rejectedCount,
            'validatedTotal'   => (int) $validatedTotal,
            'avgBasket'        => (int) $avgBasket,
            'labels'           => $labels,
            'seriesValidated'  => $seriesValidated,
            'seriesRejected'   => $seriesRejected,
            'seriesValTotal'   => $seriesValTotal,
            'latest'           => $latest,
        ]);
    }
}
