<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $pharmacy = $request->user()->pharmacy;

        $suppliers = Supplier::where('pharmacy_id', $pharmacy->id)
            ->orderBy('name')
            ->paginate(15);

        return view('pharmacist.suppliers.index', compact('suppliers', 'pharmacy'));
    }

    public function store(Request $request)
    {
        $pharmacy = $request->user()->pharmacy;

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $data['pharmacy_id'] = $pharmacy->id;

        Supplier::create($data);

        return back()->with('success', 'Fournisseur ajouté avec succès.');
    }

    public function edit(Request $request, Supplier $supplier)
    {
        $pharmacy = $request->user()->pharmacy;

        abort_unless($supplier->pharmacy_id === $pharmacy->id, 403);

        return view('pharmacist.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $pharmacy = $request->user()->pharmacy;

        abort_unless($supplier->pharmacy_id === $pharmacy->id, 403);

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $supplier->update($data);

        return redirect()
            ->route('pharmacist.suppliers.index')
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        $pharmacy = $request->user()->pharmacy;

        abort_unless($supplier->pharmacy_id === $pharmacy->id, 403);

        $supplier->delete();

        return back()->with('success', 'Fournisseur supprimé avec succès.');
    }
}
