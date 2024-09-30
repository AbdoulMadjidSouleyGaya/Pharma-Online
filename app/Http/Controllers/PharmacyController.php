<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index()
    {
        return view('pharmacies'); // Changement ici
    }
    
        

    

    public function show()
    {
        return view('pharmacie'); // Assurez-vous que le nom du fichier est correct
    }
    
}
