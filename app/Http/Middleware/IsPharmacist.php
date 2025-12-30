<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsPharmacist
{
    public function handle($request, Closure $next)
{
    $user = $request->user();

    // 1) vérifier que l'utilisateur est bien pharmacien
    $isPharmacist = $user
        && $user->roles()->whereIn('name', ['pharmacist','pharmacien'])->exists();

    if (! $isPharmacist) {
        abort(403);
    }

    // ✅ 2) FORCER le changement de mot de passe si password_is_temp = 1
    //    On laisse passer uniquement les routes du formulaire de changement
    if ((int)($user->password_is_temp ?? 0) === 1
        && ! $request->routeIs('pharmacist.password.*')) {
        return redirect()->route('pharmacist.password.form');
    }

    return $next($request);
}

}
