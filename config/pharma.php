<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sécurité : géolocalisation Pharmacien
    |--------------------------------------------------------------------------
    |
    | Si cette option est à true, le middleware CheckPharmacyLocation
    | exigera une validation de présence par géolocalisation.
    | Si elle est à false, aucune vérification géo ne sera faite.
    |
    */

    'geo_enforce' => env('PHARMA_GEO_ENFORCE', true),

    /*
    |--------------------------------------------------------------------------
    | Rayon de tolérance autour de la pharmacie (en mètres)
    |--------------------------------------------------------------------------
    |
    | Distance maximale entre la position GPS du pharmacien et la position
    | enregistrée de la pharmacie. En pratique, 50 m est trop strict dans
    | beaucoup de contextes (réseau mobile, GPS imprécis).
    |
    | Tu peux régler ce rayon dans ton .env avec PHARMA_GEO_RADIUS.
    | Par exemple :
    |   PHARMA_GEO_RADIUS=400
    |
    */

    'geo_radius_meters' => (int) env('PHARMA_GEO_RADIUS', 5000),
];
