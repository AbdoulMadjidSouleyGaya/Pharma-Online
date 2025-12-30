<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // définir la timezone de la session MySQL sur la valeur de .env (offset)
        $tz = env('APP_TIMEZONE_OFFSET', '+01:00');

        try {
            DB::statement("SET time_zone = '{$tz}'");
        } catch (\Exception $e) {
            // si la requête échoue, on ignore pour ne pas casser l'application
            // tu peux logger si tu veux : \Log::warning('Impossible de définir time_zone: '.$e->getMessage());
        }
    }
}