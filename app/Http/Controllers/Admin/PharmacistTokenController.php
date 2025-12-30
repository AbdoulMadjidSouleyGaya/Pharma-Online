<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PharmacistTokenController extends Controller
{
    /**
     * Formulaire de configuration du token API
     * + coordonnées GPS de la pharmacie liée.
     */
    public function edit(User $pharmacist)
    {
        $pharmacy = $pharmacist->pharmacy;

        return view('admin.pharmacists.token', [
            'pharmacist' => $pharmacist,
            'pharmacy'   => $pharmacy,
        ]);
    }

    /**
     * Enregistre le token API + la géolocalisation.
     */
    public function update(Request $request, User $pharmacist)
    {
        $pharmacy = $pharmacist->pharmacy;

        if (! $pharmacy) {
            return back()->withErrors([
                'pharmacy' => 'Ce pharmacien n’est associé à aucune pharmacie.',
            ]);
        }

        $data = $request->validate([
            'api_token' => ['required', 'string', 'max:255'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $pharmacy->api_token = $data['api_token'];
        $pharmacy->latitude  = $data['latitude']  ?? null;
        $pharmacy->longitude = $data['longitude'] ?? null;
        $pharmacy->save();

        return redirect()
            ->route('admin.pharmacists.index')
            ->with('status', "Token & position GPS mis à jour pour la pharmacie « {$pharmacy->name} » ✅");
    }
}
