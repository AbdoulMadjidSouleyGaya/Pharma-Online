<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:100'],
            'email'   => ['required','email','max:150'],
            'message' => ['required','string','max:2000'],
        ]);

        // TODO: envoyer un email ou enregistrer en base si tu veux.
        // \Log::info('Contact message', $data);

        return back()->with('success', 'Votre message a été envoyé. Merci !');
    }
}
