<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
{
    $credentials = $request->validate([
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (! \Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    $request->session()->regenerate();

    $user = $request->user();

    // Admin => on bascule vers le flux admin
    if ($user->roles()->where('name','admin')->exists()) {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('status', 'Veuillez vous connecter via l’espace administrateur.');
    }

    // Pharmacien ?
    $isPharmacist = $user->roles()->whereIn('name',['pharmacist','pharmacien'])->exists();

    if ($isPharmacist) {
        // 1) PREMIÈRE CONNEXION ? Mot de passe temporaire => page de changement
        if ((int)$user->password_is_temp === 1) {
            return redirect()->route('pharmacist.password.form'); // ✅
        }

        // 2) Sinon, on passe au 2FA (code à 8 chiffres)
        session()->forget(['pharmacist_2fa_ok','pharmacist_2fa_code','pharmacist_2fa_expires']);
        app(\App\Http\Controllers\Pharmacist\PharmacistAuthController::class)->resendCode($request);
        return redirect()->route('pharmacist.verify.form');
    }

    // Utilisateur standard
    return redirect()->intended(route('dashboard', absolute: false));
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
