<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Role;

class AdminPharmacistController extends Controller
{
    /**
     * Liste des pharmaciens.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $pharmacists = User::query()
            ->whereHas('roles', fn ($r) => $r->whereIn('name', ['pharmacist', 'pharmacien']))
            ->when($q, fn ($qr) => $qr->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
            ))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pharmacists.index', compact('pharmacists', 'q'));
    }

    /**
     * Formulaire de création : sélectionner une pharmacie.
     */
    public function create()
    {
        $pharmacies = Pharmacy::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.pharmacists.create', compact('pharmacies'));
    }

    /**
     * Création du compte pharmacien + envoi du mot de passe temporaire.
     */
    public function store(Request $request)
    {
        // 1) Validation
        $data = $request->validate([
            'pharmacy_id' => ['required', 'exists:pharmacies,id'],
        ]);

        // 2) Récup pharmacie
        $pharmacy = Pharmacy::findOrFail($data['pharmacy_id']);

        // 3) Empêcher les doublons (une pharmacie -> un compte pharmacien)
        $exists = User::where('pharmacy_id', $pharmacy->id)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['pharmacist', 'pharmacien']))
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'pharmacy_id' => 'Cette pharmacie possède déjà un compte pharmacien.',
            ])->withInput();
        }

        // 4) Générer le mot de passe temporaire (15 caractères) + expiration 10 min
        $plainTemp = $this->makeStrongTempPassword(15);
        $expiresAt = now()->addMinutes(10);

        // 5) Créer l’utilisateur pharmacien
        $user = User::create([
            'name'                     => 'Pharmacien Principal',
            'email'                    => $pharmacy->email,
            'password'                 => Hash::make($plainTemp),
            'pharmacy_id'              => $pharmacy->id,
            'password_is_temp'         => 1,
            'temp_password_expires_at' => $expiresAt,
        ]);

        // 6) Attacher le rôle pharmacien
        $roleId = Role::whereIn('name', ['pharmacist', 'pharmacien'])->value('id');
        if ($roleId) {
            $user->roles()->syncWithoutDetaching([$roleId]);
        }

        // 7) Envoyer l’email avec les identifiants
        try {
            Mail::to($user->email)->send(
                new \App\Mail\PharmacistCredentialsMail($pharmacy, $user, $plainTemp, $expiresAt)
            );
        } catch (\Throwable $e) {
            Log::error('Envoi email pharmacien KO: ' . $e->getMessage());
            // on laisse passer, le compte est créé malgré tout
        }

        return redirect()
            ->route('admin.pharmacists.index')
            ->with('status', 'Compte pharmacien créé. Les identifiants temporaires ont été envoyés par email.');
    }

    /**
     * Formulaire d’édition d’un pharmacien.
     */
    public function edit(User $pharmacist)
    {
        return view('admin.pharmacists.edit', [
            'pharmacist' => $pharmacist,
        ]);
    }

    /**
     * Mise à jour d’un pharmacien.
     */
    public function update(Request $request, User $pharmacist)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $pharmacist->id],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $pharmacist->name  = $data['name'];
        $pharmacist->email = $data['email'];

        if (!empty($data['password'])) {
            $pharmacist->password = Hash::make($data['password']);
        }

        $pharmacist->save();

        return redirect()
            ->route('admin.pharmacists.index')
            ->with('status', 'Le compte pharmacien a été mis à jour avec succès ✅');
    }

    /**
     * Suppression du compte pharmacien.
     */
    public function destroy(User $pharmacist)
    {
        $isPharmacist = $pharmacist->roles()->whereIn('name', ['pharmacist', 'pharmacien'])->exists();
        if (! $isPharmacist) {
            return back()->withErrors(['delete' => 'Ce compte n’est pas un pharmacien.']);
        }

        if ($pharmacist->roles()->where('name', 'admin')->exists()) {
            return back()->withErrors(['delete' => 'Impossible de supprimer un administrateur.']);
        }

        $pharmacist->roles()->detach();
        $pharmacist->delete();

        return back()->with('status', 'Pharmacien supprimé.');
    }

    /**
     * Génère un mot de passe temporaire "fort".
     */
    private function makeStrongTempPassword(int $length = 15): string
    {
        // Sans caractères ambigus (o/O/0, l/I/1)
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#%&?*!$';
        $pwd   = '';
        $max   = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $pwd .= $chars[random_int(0, $max)];
        }

        return $pwd;
    }
}
