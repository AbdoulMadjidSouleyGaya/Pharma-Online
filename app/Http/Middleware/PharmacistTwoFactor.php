<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PharmacistTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();
        if (!$u) return redirect()->route('login');

        $isPharmacist = $u->roles()->whereIn('name',['pharmacist','pharmacien'])->exists();

        if ($isPharmacist) {
            // Si pas validé 2FA -> on force la saisie du code
            if (! session('pharmacist_2fa_ok')) {
                if (! $request->routeIs('pharmacist.verify.form') &&
                    ! $request->routeIs('pharmacist.verify.submit') &&
                    ! $request->routeIs('pharmacist.verify.resend')) {
                    return redirect()->route('pharmacist.verify.form');
                }
            }
        }

        return $next($request);
    }
}
