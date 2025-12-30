<?php

namespace App\Http\Middleware;

use App\Models\Pharmacy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPharmacyLocation
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        // On récupère la pharmacie rattachée au pharmacien
        $pharmacy = $this->currentPharmacy($user);
        if (! $pharmacy) {
            // Si pas de pharmacie liée → on laisse passer pour éviter de tout bloquer
            return $next($request);
        }

        // Si la pharmacie n'a pas de coordonnées GPS → on ne bloque pas
        if (empty($pharmacy->latitude) || empty($pharmacy->longitude)) {
            return $next($request);
        }

        // Position du pharmacien stockée en session (définie depuis /pharmacist/geo)
        // adapte la clé ici si besoin : 'pharmacy_geo', 'geo', etc.
        $geo = $request->session()->get('pharmacy_geo');

        if (! $geo || empty($geo['lat']) || empty($geo['lng'])) {
            // Pas de géolocalisation en session → on redirige vers la page GEO
            return redirect()
                ->route('pharmacist.geo')
                ->with('error', "Nous avons besoin de ta position pour vérifier que tu es bien dans la pharmacie.");
        }

        $userLat = (float) $geo['lat'];
        $userLng = (float) $geo['lng'];

        $pharmacyLat = (float) $pharmacy->latitude;
        $pharmacyLng = (float) $pharmacy->longitude;

        // 🔧 Rayon max autorisé (mètres), lu depuis la config
        // cf. config/pharma.php → 'geo_radius_meters'
        $maxDistance = (int) config('pharma.geo_radius_meters', 1000);

        // Calcul de la distance en mètres
        $distanceMeters = $this->distanceInMeters(
            $userLat,
            $userLng,
            $pharmacyLat,
            $pharmacyLng
        );

        Log::info('🌍 Vérification de distance pharmacie', [
            'user_id'        => $user->id,
            'pharmacy_id'    => $pharmacy->id,
            'user_lat'       => $userLat,
            'user_lng'       => $userLng,
            'pharmacy_lat'   => $pharmacyLat,
            'pharmacy_lng'   => $pharmacyLng,
            'distance_m'     => $distanceMeters,
            'max_distance_m' => $maxDistance,
        ]);

        // ⚠️ Si le pharmacien est en dehors du rayon autorisé → on le bloque
        if ($distanceMeters > $maxDistance) {
            $km = round($maxDistance / 5000, 2);

            return redirect()
                ->route('pharmacist.geo')
                ->with('error', "Tu es trop loin de la pharmacie pour utiliser l’espace pharmacien. 
Rayon autorisé : environ {$km} km.");
        }

        // Tout est OK → on laisse la requête continuer
        return $next($request);
    }

    /**
     * Récupère la pharmacie associée au pharmacien.
     */
    protected function currentPharmacy($user): ?Pharmacy
    {
        if (method_exists($user, 'pharmacy') && $user->pharmacy) {
            return $user->pharmacy;
        }

        if (method_exists($user, 'pharmacies')) {
            return $user->pharmacies()->first();
        }

        return null;
    }

    /**
     * Calcul de distance entre 2 points GPS en mètres (Haversine).
     */
    protected function distanceInMeters(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $earthRadius = 6371000; // Rayon moyen de la Terre en mètres

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $deltaLat = $lat2 - $lat1;
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
           + cos($lat1) * cos($lat2) * sin($deltaLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
