<?php

namespace App\Http\Controllers;

use App\Models\PharmaProduct;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $q        = $request->query('q');
        $stock    = $request->query('stock');          // Disponible | Rupture | ''
        $minPrix  = $request->query('min_prix');
        $maxPrix  = $request->query('max_prix');
        $perPage  = (int) $request->query('per_page', 15);
        $sort     = $request->query('sort', 'libelle');
        $order    = $request->query('order', 'asc');

        $allowedSorts = ['libelle','prix','created_at','quantity'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'libelle';
        }

        $query = PharmaProduct::query();

        if ($q) {
            $query->where('libelle', 'like', "%{$q}%");
        }

        if ($stock) {
            $query->where('stock', $stock);
        }

        if ($minPrix !== null && $minPrix !== '') {
            $query->where('prix', '>=', (float) $minPrix);
        }

        if ($maxPrix !== null && $maxPrix !== '') {
            $query->where('prix', '<=', (float) $maxPrix);
        }

        $query->orderBy($sort, $order);

        $products = $query->paginate($perPage)->appends($request->query());

        return view('catalogue', [
            'products' => $products,
            'sort'     => $sort,
            'order'    => $order,
        ]);
    }
}
