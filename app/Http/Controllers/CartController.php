<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PharmaProduct;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = session('cart', []);
        $total = collect($cart)->reduce(function ($carry, $item) {
            $price = is_null($item['prix']) ? 0 : (float)$item['prix'];
            return $carry + ($price * $item['qty']);
        }, 0);

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:pharma_products,id'],
            'qty'        => ['nullable','integer','min:1','max:100'],
        ]);

        $qty = $data['qty'] ?? 1;

        /** @var PharmaProduct $product */
        $product = PharmaProduct::findOrFail($data['product_id']);

        // Vérifie la dispo
        $stock = strtolower(trim((string)$product->stock));
        $isDisponible = in_array($stock, ['disponible','en stock','stocké']);

        if (!$isDisponible) {
            return back()->with('error', "Ce produit est en rupture et ne peut pas être ajouté au panier.");
        }

        // Ajout au panier (session)
        $cart = session('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += $qty;
        } else {
            $cart[$product->id] = [
                'id'      => $product->id,
                'libelle' => $product->libelle,
                'prix'    => is_null($product->prix) ? 0 : (float)$product->prix,
                'qty'     => $qty,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', "« {$product->libelle} » ajouté au panier.");
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer'],
        ]);

        $cart = session('cart', []);
        unset($cart[$data['product_id']]);
        session(['cart' => $cart]);

        return back()->with('success', "Produit retiré du panier.");
    }

    public function clear(Request $request)
    {
        session()->forget('cart');
        return back()->with('success', "Panier vidé.");
    }
}
