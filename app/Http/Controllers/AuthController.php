<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Assurez-vous que le modèle User est correctement importé
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'contact' => 'required|string|max:15',
        'email' => 'nullable|email|max:255', // Email est facultatif
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Créer l'utilisateur dans la base de données
    User::create([
        'name' => $request->name,
        'surname' => $request->surname,
        'contact' => $request->contact,
        'email' => $request->email, // Peut être null si non fourni
        'password' => bcrypt($request->password),
    ]);

    // Rediriger vers la page de succès
    return redirect()->route('registration.success');
}

}
