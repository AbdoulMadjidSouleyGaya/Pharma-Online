<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    // /pharmacies  → name: pharmacies.index
    public function index(Request $request)
    {
        // On récupère éventuellement le filtre "garde=1"
        $garde = $request->boolean('garde', false);

        // Pour l’instant, on renvoie une page simple.
        // Plus tard, on listera vraiment les pharmacies (avec proximité, etc.)
        return view('pharmacies.index', compact('garde'));
    }

    // /pharmacies/{slug} (on le fera plus tard)
}
