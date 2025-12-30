<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Page Forfaits + traitement du formulaire (GET simplifié).
     */
    public function search(Request $request)
    {
        // Grille des prix (même base que la vue)
        $pricing = [
            'Essentiel' => ['Journalier' => 0,  'Standard' => 0,   'Mensuel' => 0,    'Annuel' => 0],
            'Standard'  => ['Journalier' => 25, 'Standard' => 150, 'Mensuel' => 600,  'Annuel' => 6000],
            'Premium'   => ['Journalier' => 50, 'Standard' => 300, 'Mensuel' => 1200, 'Annuel' => 12000],
        ];

        // 1) Arrivée simple sur la page => on affiche seulement la vue
        if (!$request->hasAny(['plan','duree','operator','contact'])) {
            return view('products.forfait', compact('pricing'));
        }

        // 2) Validation
        $validated = $request->validate([
            'plan'     => ['required', Rule::in(array_keys($pricing))],
            'duree'    => ['required', Rule::in(['Journalier','Standard','Mensuel','Annuel'])],
            'operator' => ['required', Rule::in(['airtel','moov','zamani','nigertelecom'])],
            'contact'  => [
                'required','string','max:60',
                function ($attribute, $value, $fail) use ($request) {
                    // Email accepté: si email -> on ne checke pas l'opérateur
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        return;
                    }
                    // Sinon, on attend un n° nigérien
                    $normalized = $this->normalizeNigerPhone($value);
                    if (!$normalized) {
                        return $fail('Numéro invalide. Entrez 8 chiffres (ex: 90 12 34 56) ou +227XXXXXXXX.');
                    }
                    if (!$this->operatorMatches($normalized, (string)$request->input('operator'))) {
                        return $fail('Le numéro ne correspond pas à l’opérateur sélectionné.');
                    }
                },
            ],
        ], [
            'plan.required'     => 'Choisissez un pack.',
            'plan.in'           => 'Pack invalide.',
            'duree.required'    => 'Choisissez une durée.',
            'duree.in'          => 'Durée invalide.',
            'operator.required' => 'Choisissez un opérateur.',
            'operator.in'       => 'Opérateur inconnu.',
            'contact.required'  => 'Entrez votre contact (téléphone ou email).',
        ]);

        // 3) Normalisation du contact si c’est un n°
        $contact = $validated['contact'];
        if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            $contact = $this->normalizeNigerPhone($contact) ?: $contact;
        }

        // 4) Calculs récap
        $plan   = $validated['plan'];
        $duree  = $validated['duree'];
        $price  = $pricing[$plan][$duree];
        $days   = ['Journalier'=>1,'Standard'=>7,'Mensuel'=>30,'Annuel'=>365][$duree];

        $payload = [
            'plan'     => $plan,
            'duree'    => $duree,
            'operator' => $validated['operator'],
            'contact'  => $contact,
            'price'    => $price,
            'days'     => $days,
        ];

        // 5) Confirmation + restitution des champs
        return back()
            ->with('forfait_ok', 'Votre configuration a été enregistrée.')
            ->with('forfait_payload', $payload)
            ->withInput($validated + ['contact' => $contact]);
    }

    /**
     * Normalise un n° nigérien en E.164: +227XXXXXXXX (8 chiffres).
     */
    private function normalizeNigerPhone(?string $raw): ?string
    {
        if (!$raw) return null;

        // Enlève espaces et caractères non numériques (+ conservé)
        $s = preg_replace('/\s+/', '', $raw);
        $s = preg_replace('/[^\d+]/', '', $s);

        // 00227 -> +227
        if (str_starts_with($s, '00227')) {
            $s = '+227' . substr($s, 5);
        }

        // +227 + 8 chiffres
        if (str_starts_with($s, '+227')) {
            $rest = substr($s, 4);
            if (preg_match('/^\d{8}$/', $rest)) {
                return '+227' . $rest;
            }
            return null;
        }

        // 8 chiffres local -> +227XXXXXXXX
        if (preg_match('/^\d{8}$/', $s)) {
            return '+227' . $s;
        }

        return null;
    }

    private function operatorMatches(string $e164, string $operator): bool
    {
        $p2 = substr($e164, 4, 2); // 2 premiers digits après +227
        // Carte des préfixes
        $map = [
            'airtel'        => ['86','87','88','89','96','97','98','99'],
            'moov'          => ['94','95','85'],
            'zamani'        => ['80','81','82','90','91','92','70','71','72'],
            'nigertelecom'  => ['83','93'],
        ];

        $allowed = $map[$operator] ?? [];
        return in_array($p2, $allowed, true);
    }

}
