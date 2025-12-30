<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PharmacistGeoController extends Controller
{
    /**
     * Rayon autorisé autour de la pharmacie (en mètres).
     * On le lit depuis la config, pour pouvoir l'ajuster facilement.
     */
    private int $radiusMeters;

    public function __construct()
    {
        // 👉 config/pharma.php doit définir 'geo_radius_meters'
        // Par défaut, on met 1000 m pour éviter les faux "trop loin".
        $this->radiusMeters = (int) config('pharma.geo_radius_meters', 5000);
    }

    /**
     * Page affichée juste après la 2FA, avant le dashboard.
     * URL typique : /pharmacist/geo
     */
    public function page(Request $request)
    {
        $user     = $request->user();
        $pharmacy = $user?->pharmacy;

        return view('pharmacist.geo', [
            'user'     => $user,
            'pharmacy' => $pharmacy,
            'radius'   => $this->radiusMeters,      // utilisé dans ta vue
            'hint'     => session('geo_hint'),      // éventuel message d’info
        ]);
    }

    /**
     * Endpoint appelé en AJAX depuis le bouton
     * "📡 Lancer la vérification de présence".
     * URL : route('pharmacist.geo.check')
     */
    public function check(Request $request)
    {
        $user     = $request->user();
        $pharmacy = $user?->pharmacy;

        if (! $user || ! $pharmacy) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Aucune pharmacie associée à ce compte.',
            ], 1000);
        }

        if (! $pharmacy->latitude || ! $pharmacy->longitude) {
            return response()->json([
                'status'  => 'error',
                'message' => 'La pharmacie n’a pas de coordonnées GPS configurées (latitude / longitude).',
            ], 1000);
        }

        // Validation des données envoyées par le JS (lat/lng navigateur)
        $data = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $distance = $this->distanceInMeters(
            (float) $pharmacy->latitude,
            (float) $pharmacy->longitude,
            (float) $data['latitude'],
            (float) $data['longitude'],
        );

            // ✅ Assez proche → on valide la présence
            if ($distance <= $this->radiusMeters) {

                // On enregistre la validation dans la session
                $request->session()->put('pharmacy_geo', [
                    'ok'          => true,
                    'pharmacy_id' => $pharmacy->id,
                    'max_radius'  => $this->radiusMeters,
                    'distance'    => $distance,
                    'lat'         => (float) $data['latitude'],
                    'lng'         => (float) $data['longitude'],
                ]);

                return response()->json([
                    'status'   => 'ok',
                    'message'  => 'Présence validée : vous êtes bien dans la pharmacie.',
                    'distance' => round($distance, 1),
                    'redirect' => route('pharmacist.dashboard'), // 🔥 REDIRECTION ICI
                ]);
            }


        // ❌ Trop loin → on invalide la présence dans la session
        $request->session()->forget('pharmacy_geo');

        return response()->json([
            'status'   => 'error',
            'message'  => 'Vous semblez trop loin de la pharmacie (≈ '
                          . round($distance, 1) . ' m, max autorisé '
                          . $this->radiusMeters . ' m).',
            'distance' => round($distance, 1),
        ], 403);
    }

    /**
     * Distance entre 2 points (lat/lng) en mètres (formule de Haversine).
     */
    private function distanceInMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // mètres

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
