<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\PharmaProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductCatalog
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = PharmaProduct::query();

        // Recherche (simple)
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where('libelle', 'like', '%' . $search . '%');
        }

        // Tri FIXE (plus de filtres côté UI)
        $query->orderBy('libelle', 'asc');

        // Pagination FIXE à 24 par page
        $perPage = 24;

        return $query->paginate($perPage)->appends(
            $request->only('q') // on ne conserve que q dans la query string
        );
    }
}