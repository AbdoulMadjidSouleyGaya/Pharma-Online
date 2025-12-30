<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->hasRole('admin')) {
            // S'il n'a pas encore passé la 2FA → page de saisie du code
            if (!session('admin_2fa_passed')) {
                return redirect()->route('admin.verify.form');
            }
            // Sinon, vers le dashboard admin
            return redirect()->route('admin.dashboard');
        }
        return $next($request);
    }
}
