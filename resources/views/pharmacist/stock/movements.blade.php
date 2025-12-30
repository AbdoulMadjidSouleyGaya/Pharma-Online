<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Mouvements — {{ $product->libelle }}</title>

    <style>
        body{
            margin:0;
            min-height:100vh;
            background:#0f172a;
            font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
            color:#e5e7eb;
        }
        .wrap{max-width:1100px;margin:24px auto;padding:0 18px;}
        a{color:#93c5fd;text-decoration:none;}
        a:hover{text-decoration:underline;}
        table{width:100%;border-collapse:collapse;font-size:13px;margin-top:14px;}
        th,td{padding:8px 9px;border-bottom:1px solid rgba(148,163,184,.4);}
        th{font-size:11px;text-transform:uppercase;color:#9ca3af;}
        .badge{
            display:inline-flex;padding:2px 8px;border-radius:999px;font-size:11px;
        }
        .badge-in{background:rgba(22,163,74,.2);color:#bbf7d0;}
        .badge-out{background:rgba(248,113,113,.2);color:#fecaca;}
        .badge-adj{background:rgba(59,130,246,.2);color:#bfdbfe;}
    </style>
</head>
<body>
<div class="wrap">
    <p>
        <a href="{{ route('pharmacist.stock.index') }}">← Retour à la gestion de stock</a>
    </p>

    <h1 style="font-size:22px;font-weight:700;margin-bottom:4px;">
        Mouvements de stock — {{ $product->libelle }}
    </h1>
    <p style="color:#9ca3af;font-size:13px;">
        Pharmacie : {{ $product->pharmacy->name ?? '—' }}
    </p>

    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Quantité</th>
            <th>Avant</th>
            <th>Après</th>
            <th>Source</th>
            <th>Référence</th>
            <th>Commentaire</th>
        </tr>
        </thead>
        <tbody>
        @forelse($movements as $m)
            @php
                $badgeClass = 'badge-adj';
                $label = 'Ajustement';
                if ($m->type === 'IN') { $badgeClass = 'badge-in'; $label = 'Entrée'; }
                if ($m->type === 'OUT') { $badgeClass = 'badge-out'; $label = 'Sortie'; }
            @endphp
            <tr>
                <td>{{ $m->created_at?->format('d/m/Y H:i') }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $label }}</span></td>
                <td>{{ $m->quantity }}</td>
                <td>{{ $m->previous_quantity }}</td>
                <td>{{ $m->new_quantity }}</td>
                <td>{{ $m->source ?? '—' }}</td>
                <td>{{ $m->reference ?? '—' }}</td>
                <td>{{ $m->comment ?? '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;color:#9ca3af;padding:10px;">
                    Aucun mouvement enregistré pour ce produit.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:10px;">
        {{ $movements->links() }}
    </div>
</div>
</body>
</html>
