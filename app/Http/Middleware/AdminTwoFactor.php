<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->get('admin_2fa_passed')) {
            return redirect()->route('admin.verify.form');
        }
        return $next($request);
    }
}
