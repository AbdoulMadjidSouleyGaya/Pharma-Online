<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Commandes — Espace Pharmacien</title>
    <style>
        :root{
            --bg:#0b1220; --ink:#eef4ff; --muted:#a7b5cf; --line:#ffffff22;
            --panel:#0f1b3a; --panel2:#132650; --accent:#38bdf8;
            --ok:#10b981; --ko:#ef4444; --warn:#f59e0b; --brand:#2563eb;
            --radius:16px; --shadow:0 14px 40px rgba(0,0,0,.28);
        }
        *{box-sizing:border-box}
        html,body{
            margin:0;
            background:
                radial-gradient(1400px 700px at 60% -20%, #1e3a8a33, transparent 55%),
                linear-gradient(160deg,#0b1020 0%, #0b1220 60%, #0a1528 100%);
            color:var(--ink);
            font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
        }
        a{text-decoration:none;color:inherit}
        .wrap{max-width:1200px;margin:28px auto;padding:0 18px}

        /* Header */
        .top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:18px;flex-wrap:wrap}
        .top-left h1{margin:0;font-size:26px;font-weight:900;letter-spacing:.2px}
        .top-left p{margin:6px 0 0;color:var(--muted);font-size:14px}
        .top-right{display:flex;flex-direction:column;align-items:flex-end;gap:8px}
        .pill{
            display:inline-flex;align-items:center;gap:6px;padding:6px 10px;
            border-radius:999px;border:1px solid var(--line);background:#ffffff10;
            font-size:11px;text-transform:uppercase;letter-spacing:.3px;
        }

        /* Card + style */
        .card{
            background:linear-gradient(180deg,var(--panel),var(--panel2));
            border:1px solid var(--line);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            padding:18px;
        }

        /* Tabs */
        .tabs{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px}
        .tab{
            display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:999px;
            border:1px solid #1f2a4d;font-size:12px;cursor:pointer;background:#0d1a36;color:#cbd5f5;
            transition:background .15s ease,border-color .15s ease,transform .08s ease;
        }
        .tab:hover{transform:translateY(-1px);border-color:#3b82f6}
        .tab.active{background:linear-gradient(135deg,#2563eb,#38bdf8);border-color:#38bdf8;color:#0b1220}
        .tab span.badge{
            min-width:22px;text-align:center;border-radius:999px;padding:2px 6px;font-weight:800;font-size:11px;background:#020617;color:#e5e7eb;
        }

        /* Table */
        .table-wrap{margin-top:10px;border-radius:14px;border:1px solid #1f2a4d;overflow:hidden;background:rgba(9,11,26,.9)}
        table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px}
        thead{background:rgba(15,23,42,.9)}
        th,td{padding:10px 12px;border-bottom:1px solid #111827}
        th{text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.25px;color:#9fb4d4}
        tbody tr:hover{background:rgba(15,23,42,.95)}
        .muted{color:var(--muted);font-size:12px}

        .stat-badge{
            display:inline-flex;align-items:center;gap:6px;padding:4px 9px;border-radius:999px;
            font-size:11px;font-weight:800;border:1px solid var(--line);
        }
        .stat-wait{background:#f59e0b26;border-color:#f59e0b66;color:#fed7aa}
        .stat-ok{background:#10b98126;border-color:#10b98166;color:#bbf7d0}
        .stat-ko{background:#ef444426;border-color:#ef444466;color:#fecaca}

        /* Btn */
        .btn{
            appearance:none;border-radius:999px;border:1px solid #334155;background:#0f172a;color:#e5e7eb;
            font-size:12px;padding:6px 12px;display:inline-flex;align-items:center;gap:6px;cursor:pointer;
            transition:background .15s ease,transform .08s ease,border-color .15s ease;
        }
        .btn:hover{background:#1f2937;border-color:#3b82f6;transform:translateY(-1px)}
        .btn-sm{padding:5px 10px;font-size:11px}
        .btn-primary{background:linear-gradient(135deg,#2563eb,#38bdf8);border-color:#38bdf8;color:#0b1220}

        /* Empty */
        .empty{text-align:center;padding:32px 20px}
        .empty h3{margin:0 0 4px;font-size:16px}
        .empty p{margin:0;font-size:13px;color:var(--muted)}
    </style>
</head>

<body>
<main class="wrap">
    @php
        $statusMap = [
            'en_attente'=>['class'=>'stat-wait','label'=>'En attente'],
            'en_cours'  =>['class'=>'stat-wait','label'=>'En cours'],
            'valide'    =>['class'=>'stat-ok','label'=>'Validée'],
            'rejete'    =>['class'=>'stat-ko','label'=>'Rejetée'],
            'annulee'   =>['class'=>'stat-ko','label'=>'Annulée'],
        ];
    @endphp

    {{-- FLASH --}}
    @if (session('success'))
        <div class="card" style="border-color:#10b981;background:#064e3b;margin-bottom:12px">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="card" style="border-color:#ef4444;background:#450a0a;margin-bottom:12px">
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="top">
        <div class="top-left">
            <h1>Commandes clients</h1>
            <p>Liste des commandes reçues pour <strong>{{ $pharmacy->name }}</strong>.</p>
        </div>
        <div class="top-right">
            <span class="pill">🏥 {{ $pharmacy->name }}</span>
            <a href="{{ route('pharmacist.dashboard') }}" class="btn btn-sm">← Retour tableau de bord</a>
        </div>
    </div>

    <div class="card">

        {{-- ONGLETS FILTRES --}}
        <div class="tabs">
            <a class="tab {{ $filter==='pending'?'active':'' }}" href="?status=pending">En attente <span class="badge">{{ $counts['pending'] ?? 0 }}</span></a>
            <a class="tab {{ $filter==='validated'?'active':'' }}" href="?status=validated">Validées <span class="badge">{{ $counts['validated'] ?? 0 }}</span></a>
            <a class="tab {{ $filter==='rejected'?'active':'' }}" href="?status=rejected">Rejetées <span class="badge">{{ $counts['rejected'] ?? 0 }}</span></a>
        </div>

        {{-- TABLEAU --}}
        <div class="table-wrap">
            @if($orders->count())
                <table>
                    <thead>
                    <tr>
                        <th>N°</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Articles</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        @php
                            $map = $statusMap[$order->status] ?? $statusMap['en_attente'];
                            $client = optional($order->user)->name ?? 'Client';
                            $created = $order->created_at ? $order->created_at->format('d/m/Y H:i') : '—';
                        @endphp
                        <tr>
                            <td>#{{ $order->number }}</td>
                            <td>{{ $client }}</td>
                            <td>{{ $created }}</td>
                            <td>{{ $order->count }}</td>
                            <td>{{ number_format($order->total,0,',',' ') }} F</td>
                            <td><span class="stat-badge {{ $map['class'] }}">{{ $map['label'] }}</span></td>
                            <td style="text-align:right">
                                <a href="{{ route('pharmacist.orders.show',$order) }}" class="btn btn-sm btn-primary">
                                    🧾 Détails
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">
                    <h3>Aucune commande</h3>
                    <p>Aucune commande trouvée pour ce filtre.</p>
                </div>
            @endif
        </div>
    </div>
</main>
</body>
</html>
