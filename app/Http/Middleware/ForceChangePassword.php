<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceChangePassword
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();
        if (!$u) return redirect()->route('login');

        $isPharmacist = $u->roles()->whereIn('name',['pharmacist','pharmacien'])->exists();

        // Si pharmacien et mot de passe temporaire -> on force la page de changement
        if ($isPharmacist && (int)$u->password_is_temp === 1) {
            // Autorise déjà la page de changement pour éviter boucle
            if (! $request->routeIs('pharmacist.password.form') && ! $request->routeIs('pharmacist.password.change')) {
                return redirect()->route('pharmacist.password.form');
            }
        }

        return $next($request);
    }
}
