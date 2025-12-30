<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Connexion de diffusion par défaut
    |--------------------------------------------------------------------------
    |
    | Cette option contrôle la connexion de diffusion par défaut utilisée
    | par ton application. Tu peux la changer dans ton fichier .env via
    | BROADCAST_CONNECTION=...
    |
    */

    'default' => env('BROADCAST_CONNECTION', env('BROADCAST_DRIVER', 'log')),

    /*
    |--------------------------------------------------------------------------
    | Connexions de diffusion
    |--------------------------------------------------------------------------
    |
    | Ici tu peux définir toutes les connexions de diffusion qui seront
    | utilisées pour diffuser des événements. Laravel supporte Pusher,
    | Ably, Redis, log et un driver null.
    |
    */

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key'    => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster'   => env('PUSHER_APP_CLUSTER', 'mt1'),
                'host'      => env('PUSHER_HOST') ?: 'api-' . env('PUSHER_APP_CLUSTER', 'mt1') . '.pusher.com',
                'port'      => env('PUSHER_PORT', 443),
                'scheme'    => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
            ],
            // options supplémentaires pour le client Pusher PHP si besoin
            'client_options' => [
                // 'verify' => false,
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key'    => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver'     => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
