<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Ici tu enregistres les canaux de diffusion que ton application supporte.
| Le canal est accessible seulement si la callback retourne true.
|
*/

/**
 * Canal généré par défaut par Laravel.
 * (utile si un jour tu veux écouter des événements liés à un user précis)
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Canal privé pour les commandes d'une pharmacie :
 *   pharmacies.{pharmacyId}.orders
 *
 * Conditions :
 *  - L'utilisateur doit être authentifié (guard "web" par défaut).
 *  - L'utilisateur doit être PHARMACIEN (rôle "pharmacist" ou "pharmacien").
 *  - L'utilisateur doit appartenir à la pharmacie {pharmacyId}.
 */
Broadcast::channel('pharmacies.{pharmacyId}.orders', function ($user, int $pharmacyId) {
    if (! $user) {
        return false;
    }

    // 1) Vérifier que l'utilisateur est bien pharmacien
    $isPharmacist = $user->roles()
        ->whereIn('name', ['pharmacist', 'pharmacien'])
        ->exists();

    if (! $isPharmacist) {
        return false;
    }

    // 2) Vérifier qu'il est rattaché à cette pharmacie
    $hasPharmacy = false;

    // Cas le plus courant : users.pharmacy_id
    if (! is_null($user->pharmacy_id ?? null)) {
        $hasPharmacy = ((int) $user->pharmacy_id === (int) $pharmacyId);
    }

    // Si un jour tu passes sur une relation many-to-many (user ↔ pharmacies),
    // on garde cette sécurité :
    if (! $hasPharmacy && method_exists($user, 'pharmacies')) {
        $hasPharmacy = $user->pharmacies()
            ->where('pharmacies.id', (int) $pharmacyId)
            ->exists();
    }

    return $hasPharmacy;
});
