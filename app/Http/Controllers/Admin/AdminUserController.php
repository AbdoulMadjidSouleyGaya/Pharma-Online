<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($qq) use ($like) {
                    $qq->where('name', 'like', $like)
                       ->orWhere('email', 'like', $like);
                });
            })
            ->latest() // created_at desc
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['delete' => "Vous ne pouvez pas supprimer votre propre compte."]);
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return back()->withErrors(['delete' => "Impossible de supprimer un compte administrateur."]);
        }

        $user->delete();

        return back()->with('status', "Utilisateur supprimé.");
    }

    public function export(Request $request): StreamedResponse
    {
        $q = trim((string) $request->query('q', ''));

        $fileName = 'utilisateurs_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($q) {
            $out = fopen('php://output', 'w');

            // BOM UTF-8 pour bien ouvrir dans Excel (FR)
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes (séparateur ; adapté à Excel FR)
            fputcsv($out, ['Nom', 'Email', 'Créé le'], ';');

            $query = User::query()
                ->when($q, function ($query) use ($q) {
                    $like = '%'.$q.'%';
                    $query->where(function ($qq) use ($like) {
                        $qq->where('name', 'like', $like)
                           ->orWhere('email', 'like', $like);
                    });
                })
                ->orderByDesc('id');

            // Stream en chunks pour éviter la charge mémoire
            $query->chunk(1000, function ($chunk) use ($out) {
                foreach ($chunk as $u) {
                    fputcsv($out, [
                        $u->name,
                        $u->email,
                        optional($u->created_at)->format('Y-m-d H:i:s'),
                    ], ';');
                }
            });

            fclose($out);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }
}
