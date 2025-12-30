<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminLoginCodeMail;
use App\Models\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        // Si déjà connecté + 2FA OK -> dashboard
        if (Auth::check() && session('admin_2fa_passed') && Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (!Auth::attempt($credentials, false)) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->withInput();
        }

        $user = $request->user();

        if (!$user->hasRole('admin')) {
            Auth::logout();
            return back()->withErrors(['email' => 'Accès réservé à l’administrateur.'])->withInput();
        }

        // OK : on génère un code 6 chiffres et on l’envoie par email
        $code = (string)random_int(100000, 999999);

        // supprime les anciens codes non expirés pour ce user/purpose
        TwoFactorCode::where('user_id', $user->id)
            ->where('purpose', 'admin_login')
            ->delete();

        TwoFactorCode::create([
            'user_id'   => $user->id,
            'purpose'   => 'admin_login',
            'code'      => Hash::make($code),  // on hash le code
            'expires_at'=> now()->addMinutes(10),
        ]);
        session(['admin_2fa_last_sent' => now()->toIso8601String()]);

        // Envoi email (en local: va dans storage/logs/laravel.log)
        Mail::to($user->email)->send(new AdminLoginCodeMail($code));
        \Log::info('admin.2fa.generated', [
            'user_id' => $user->id,
            'email'   => $user->email,
            // NE PAS logger le code lui-même !
        ]);

        // reset le flag de session
        session()->forget('admin_2fa_passed');

        return redirect()->route('admin.verify.form')->with('status','Un code a été envoyé à votre email.');
    }

    public function showVerifyForm()
{
    if (!auth()->check() || !auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.login')->withErrors(['email' => 'Veuillez vous reconnecter.']);
    }

    $record = \App\Models\TwoFactorCode::where('user_id', auth()->id())
        ->where('purpose', 'admin_login')
        ->latest('id')
        ->first();

    $expiresAt = optional($record)->expires_at; // Carbon|null
    // sentAt = session (si présent) sinon created_at du record
    $sentAt = session('admin_2fa_last_sent')
        ? \Carbon\Carbon::parse(session('admin_2fa_last_sent'))
        : optional($record)->created_at;

    return view('admin.verify', compact('expiresAt', 'sentAt'));
}


    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required','digits:6'],
        ]);

        $user = $request->user();
        $record = TwoFactorCode::where('user_id', $user->id)
            ->where('purpose', 'admin_login')
            ->latest('id')
            ->first();

        if (!$record || $record->expires_at->isPast()) {
            return back()->withErrors(['code' => 'Code expiré. Demandez un nouveau code.']);
        }

        if (!Hash::check($request->code, $record->code)) {
            return back()->withErrors(['code' => 'Code invalide.'])->withInput();
        }

        // OK
        session(['admin_2fa_passed' => true]);
        // optionnel: supprimer le code utilisé
        $record->delete();

        return redirect()->route('admin.dashboard');
    }

    public function resendCode(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return redirect()->route('admin.login');
        }

        $code = (string)random_int(100000, 999999);

        TwoFactorCode::where('user_id', $user->id)
            ->where('purpose', 'admin_login')
            ->delete();

        TwoFactorCode::create([
            'user_id'   => $user->id,
            'purpose'   => 'admin_login',
            'code'      => \Illuminate\Support\Facades\Hash::make($code),
            'expires_at'=> now()->addMinutes(10),
        ]);
        session(['admin_2fa_last_sent' => now()->toIso8601String()]);

        Mail::to($user->email)->send(new AdminLoginCodeMail($code));

        return back()->with('status', 'Nouveau code envoyé.');
    }

    public function logout()
    {
        Auth::logout();
        session()->forget('admin_2fa_passed');
        return redirect()->route('admin.login');
    }
}
