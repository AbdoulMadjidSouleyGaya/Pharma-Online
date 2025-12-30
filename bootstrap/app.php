<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ... garde ce qu’il y a déjà, puis ajoute :
        $middleware->alias([
            'is_admin'  => \App\Http\Middleware\IsAdmin::class,
            'admin.2fa' => \App\Http\Middleware\AdminTwoFactor::class,
            'not_admin' => \App\Http\Middleware\NotAdmin::class,   // on l’ajoute juste après l’avoir créé (voir plus bas)
            'is_pharmacist' => \App\Http\Middleware\IsPharmacist::class,
            'force_change_pwd' => \App\Http\Middleware\ForceChangePassword::class,
            'pharmacist.2fa' => \App\Http\Middleware\PharmacistTwoFactor::class,
             'pharmacy.geo' => \App\Http\Middleware\CheckPharmacyLocation::class,
        ]);
    })
    ->withMiddleware(function (Illuminate\Foundation\Configuration\Middleware $middleware) {
        // ...
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        // ...
    })
    ->create();