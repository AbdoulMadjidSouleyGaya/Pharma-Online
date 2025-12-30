<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;


class AdminPharmacyController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $pharmacies = Pharmacy::query()
            ->when($q, function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($qq) use ($like) {
                    $qq->where('name','like',$like)
                       ->orWhere('district','like',$like)
                       ->orWhere('address','like',$like)
                       ->orWhere('phone','like',$like)
                       ->orWhere('email','like',$like);
                });
            })
            ->latest() // created_at desc
            ->paginate(15)
            ->withQueryString();

        return view('admin.pharmacies.index', compact('pharmacies','q'));
    }

    public function create()
    {
        return view('admin.pharmacies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        Pharmacy::create($data);

        return redirect()->route('admin.pharmacies.index')
            ->with('status','Pharmacie ajoutée.');
    }

    public function edit(Pharmacy $pharmacy)
    {
        return view('admin.pharmacies.edit', compact('pharmacy'));
    }

    public function update(Request $request, Pharmacy $pharmacy): RedirectResponse
    {
        $data = $this->validated($request);
        $pharmacy->update($data);

        return redirect()->route('admin.pharmacies.index')
            ->with('status','Pharmacie mise à jour.');
    }

    public function destroy(Pharmacy $pharmacy): RedirectResponse
    {
        $pharmacy->delete();
        return back()->with('status','Pharmacie supprimée.');
    }

    private function validated(Request $request): array
{
    return $request->validate([
        'name'     => ['required','string','max:150'],
        'district' => ['required','string','max:150'],
        'address'  => ['nullable','string','max:255'],
        'phone'    => ['nullable','string','max:30'],
        'email'    => ['nullable','email','max:150'],
        // plus de latitude/longitude/is_on_duty
    ]);
}
public function export(Request $request): StreamedResponse
{
    $q = trim((string) $request->query('q', ''));
    $fileName = 'pharmacies_'.now()->format('Y-m-d_H-i-s').'.csv';

    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
    ];

    $callback = function () use ($q) {
        $out = fopen('php://output', 'w');

        // BOM UTF-8 pour Excel FR
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        // En-têtes (séparateur ; compatible Excel FR)
        fputcsv($out, ['Nom', 'Quartier', 'Adresse', 'Téléphone', 'Email', 'Ajoutée le'], ';');

        $query = \App\Models\Pharmacy::query()
            ->when($q, function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($qq) use ($like) {
                    $qq->where('name','like',$like)
                       ->orWhere('district','like',$like)
                       ->orWhere('address','like',$like)
                       ->orWhere('phone','like',$like)
                       ->orWhere('email','like',$like);
                });
            })
            ->orderByDesc('id');

        // Stream par paquets pour limiter la mémoire
        $query->chunk(500, function ($chunk) use ($out) {
            foreach ($chunk as $p) {
                fputcsv($out, [
                    $p->name,
                    $p->district,
                    $p->address,
                    $p->phone,
                    $p->email,
                    optional($p->created_at)->format('Y-m-d H:i:s'),
                ], ';');
            }
        });

        fclose($out);
    };

    return response()->streamDownload($callback, $fileName, $headers);
}


}
