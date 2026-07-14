<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $recipient = config('mail.from.address', env('MAIL_FROM_ADDRESS', 'abdoulmadjidsouley@gmail.com'));
        $subject = !empty($data['subject'])
            ? 'Nouveau message de contact - ' . $data['subject']
            : 'Nouveau message de contact - PharmaOnline';

        $mailBody = "Un nouveau message a été envoyé depuis la page Contact de PharmaOnline.\n\n"
            . "Nom complet : {$data['name']}\n"
            . "Email : {$data['email']}\n"
            . "Téléphone : " . ($data['phone'] ?? 'Non renseigné') . "\n"
            . "Sujet : " . ($data['subject'] ?? 'Non renseigné') . "\n"
            . "Adresse IP : {$request->ip()}\n\n"
            . "Message :\n{$data['message']}\n";

        try {
            Mail::raw($mailBody, function ($message) use ($recipient, $subject, $data) {
                $message->to($recipient)
                    ->subject($subject)
                    ->replyTo($data['email'], $data['name']);
            });

            Log::info('PharmaOnline contact message sent successfully', [
                'recipient' => $recipient,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'subject' => $data['subject'] ?? null,
                'message' => $data['message'],
                'ip' => $request->ip(),
            ]);

            return back()->with('success', 'Votre message a été envoyé avec succès. Merci !');
        } catch (\Throwable $e) {
            Log::error('PharmaOnline contact email sending failed', [
                'recipient' => $recipient,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'subject' => $data['subject'] ?? null,
                'message' => $data['message'],
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', "L'envoi du message a échoué. Vérifiez la configuration email puis réessayez.");
        }
    }
}
