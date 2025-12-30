<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PharmacistManageController extends Controller
{
    /**
     * Petit tableau de bord "outils rapides" du pharmacien.
     * On renvoie la vue pharmacist.manage si tu l’as, sinon on fait un fallback.
     */
    public function index(Request $request)
    {
        // si tu as déjà une vue resources/views/pharmacist/manage.blade.php on la renvoie
        if (view()->exists('pharmacist.manage')) {
            return view('pharmacist.manage');
        }

        // sinon on renvoie une vue basique inline (ça évite l’erreur)
        return view('pharmacist.fallback-manage');
    }
}
