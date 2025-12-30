<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PharmacistAuthController extends Controller
{
    /** ---- Mot de passe : forcer le changement ---- */

    public function showChangePasswordForm()
{
    return view('pharmacist.password-change', ['user' => auth()->user()]);
}
    public function changePassword(Request $request)
{
    $request->validate([
        'password' => ['required','string','min:8','confirmed'],
    ]);

    $u = $request->user();
    $u->password = \Illuminate\Support\Facades\Hash::make($request->input('password'));
    $u->password_is_temp = 0;                 // ✅ on enlève le flag
    $u->temp_password_expires_at = null;
    $u->save();

    // on force la reconnexion
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('status', 'Mot de passe changé. Veuillez vous reconnecter.');
}
    /** ---- 2FA ---- */

    public function showVerifyForm(Request $request)
    {
        // Si pas déjà émis pour cette session, on émet un code
        if (! session('pharmacist_2fa_code') || now()->greaterThan(session('pharmacist_2fa_expires'))) {
            $this->issueAndSend2faCode($request);
        }

        $expiresAt = session('pharmacist_2fa_expires'); // instance Carbon ou string
        $secondsLeft = $expiresAt ? max(0, now()->diffInSeconds(Carbon::parse($expiresAt), false)) : 0;

        return view('pharmacist.verify', ['secondsLeft' => $secondsLeft]);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => ['required','string','min:4','max:10']]);

        $code   = $request->input('code');
        $expect = session('pharmacist_2fa_code');
        $expAt  = session('pharmacist_2fa_expires');

        if (!$expect || !$expAt || now()->gt(Carbon::parse($expAt))) {
            return back()->withErrors(['code' => 'Code expiré. Cliquez sur “Renvoyer le code”.']);
        }
        if (! hash_equals((string)$expect, (string)$code)) {
            return back()->withErrors(['code' => 'Code incorrect.']);
        }

        // Succès
        session(['pharmacist_2fa_ok' => true]);

        return redirect()->route('pharmacist.geo.page')
            ->with('status', 'Authentification à deux facteurs réussie. Merci de valider votre présence dans la pharmacie ✅');
    }

    public function resendCode(Request $request)
    {
        $this->issueAndSend2faCode($request);
        return back()->with('status','Nouveau code envoyé.');
    }

    /** Génère + envoie un code 2FA (email) et le stocke en session */
    private function issueAndSend2faCode(Request $request): void
{
    $user = $request->user();
    $code = str_pad((string)random_int(0, 99_999_999), 8, '0', STR_PAD_LEFT); // ✅ 8 chiffres
    $exp  = now()->addMinutes(10);

    session([
        'pharmacist_2fa_ok'      => false,
        'pharmacist_2fa_code'    => $code,
        'pharmacist_2fa_expires' => $exp,
    ]);

    try {
        \Mail::raw(
            "Votre code 2FA PharmaOnline : {$code}\nValide jusqu’à ".$exp->format('d/m/Y H:i').".",
            function($m) use ($user) {
                $m->to($user->email)->subject('Code de vérification (2FA) - PharmaOnline');
            }
        );
    } catch (\Throwable $e) {
        \Log::error('2FA email pharmacien KO: '.$e->getMessage());
    }
}
    
}
