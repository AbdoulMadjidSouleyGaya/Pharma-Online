<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submitForm(Request $request)
{
    // Valider les champs du formulaire
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'message' => 'required|string',
    ]);

    // Collecter les données du formulaire
    $data = [
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'user_message' => $request->input('message') // Renommé en user_message
    ];
    
    // Envoyer l'email
    Mail::send('emails.contact', $data, function ($message) use ($data) {
        $message->to('ton_email@example.com') // Remplace par ton adresse email
                ->subject('Nouveau message de contact de ' . $data['name']);
    });

    // Retourner à la page précédente avec un message de succès
    return redirect()->back()->with('success', 'Votre message a été envoyé avec succès.');
}

}
