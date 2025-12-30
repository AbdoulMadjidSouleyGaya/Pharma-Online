<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Rapports — Espace Pharmacien</title>
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

    .card{
      background:linear-gradient(180deg,var(--panel),var(--panel2));
      border:1px solid var(--line);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      padding:18px;
    }

    /* Header */
    .top{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:18px;
      margin-bottom:18px;
      flex-wrap:wrap;
    }
    .top-left h1{margin:0;font-size:26px;font-weight:900;letter-spacing:.2px}
    .top-left p{margin:6px 0 0;color:var(--muted);font-size:14px}
    .top-right{display:flex;flex-direction:column;align-items:flex-end;gap:8px}
    .pill{
      display:inline-flex;align-items:center;gap:6px;padding:6px 10px;
      border-radius:999px;border:1px solid var(--line);background:#ffffff10;
      font-size:11px;text-transform:uppercase;letter-spacing:.3px;
    }

    .btn{
      appearance:none;border-radius:999px;border:1px solid #334155;background:#0f172a;color:#e5e7eb;
      font-size:12px;padding:6px 12px;display:inline-flex;align-items:center;gap:6px;cursor:pointer;
      transition:background .15s ease,transform .08s ease,border-color .15s ease;
    }
    .btn:hover{background:#1f2937;border-color:#3b82f6;transform:translateY(-1px)}
    .btn-sm{padding:5px 10px;font-size:11px}

    /* Filtres période */
    .filters{
      display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;
    }
    .filter-pill{
      padding:6px 11px;border-radius:999px;font-size:11px;
      border:1px solid #1f2a4d;background:#0d1a36;color:#cbd5f5;
      text-transform:uppercase;letter-spacing:.25px;cursor:pointer;
      display:inline-flex;align-items:center;gap:8px;
      transition:background .15s ease,border-color .15s ease,transform .08s ease;
    }
    .filter-pill:hover{transform:translateY(-1px);border-color:#3b82f6}
    .filter-pill.active{
      background:linear-gradient(135deg,#2563eb,#38bdf8);
      border-color:#38bdf8;color:#0b1220;
    }

    /* KPI grid */
    .kpi-grid{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:14px;
      margin-bottom:18px;
    }
    @media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:600px){.kpi-grid{grid-template-columns:1fr;}}

    .kpi{
      background:#0d1a36;
      border-radius:14px;
      border:1px solid #1f2937;
      padding:12px 14px;
    }
    .kpi-label{
      font-size:11px;color:#9fb4d4;text-transform:uppercase;letter-spacing:.25px;
    }
    .kpi-value{
      margin-top:6px;font-size:22px;font-weight:900;
    }
    .kpi-sub{
      margin-top:4px;font-size:12px;color:var(--muted);
    }

    /* Layout 2 colonnes */
    .grid{
      display:grid;
      grid-template-columns:minmax(0,2.1fr) minmax(0,1.3fr);
      gap:18px;
    }
    @media(max-width:960px){.grid{grid-template-columns:1fr;}}

    .section-title{
      margin:0 0 10px;
      font-size:15px;
      font-weight:800;
      letter-spacing:.2px;
    }
    .section-sub{margin:0 0 14px;font-size:12px;color:var(--muted)}

    /* Table top produits */
    .table-wrap{
      border-radius:14px;
      border:1px solid #1f2a4d;
      overflow:hidden;
      background:rgba(9,11,26,.9);
    }
    table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px}
    thead{background:rgba(15,23,42,.9)}
    th,td{padding:9px 11px;border-bottom:1px solid #111827}
    th{
      text-align:left;font-size:11px;text-transform:uppercase;
      letter-spacing:.25px;color:#9fb4d4;
    }
    tbody tr:hover{background:rgba(15,23,42,.95)}
    .muted{color:var(--muted);font-size:12px}

    /* Statut / répartition */
    .status-badge{
      display:inline-flex;align-items:center;gap:6px;padding:4px 9px;border-radius:999px;
      border:1px solid var(--line);font-size:11px;font-weight:800;
    }
    .s-wait{background:#f59e0b26;border-color:#f59e0b66;color:#fed7aa}
    .s-ok{background:#10b98126;border-color:#10b98166;color:#bbf7d0}
    .s-ko{background:#ef444426;border-color:#ef444466;color:#fecaca}

    .status-row{
      display:flex;align-items:center;justify-content:space-between;
      gap:10px;font-size:13px;padding:6px 0;
    }
    .bar{
      flex:1;height:6px;border-radius:999px;
      background:linear-gradient(90deg,#1e293b,#0b1220);
      overflow:hidden;
    }
    .bar-inner{
      height:100%;border-radius:999px;
      background:linear-gradient(90deg,#38bdf8,#22c55e);
      transition:width .25s ease;
    }

    .empty{
      padding:24px 16px;text-align:center;
    }
    .empty h3{margin:0 0 4px;font-size:15px}
    .empty p{margin:0;font-size:12px;color:var(--muted)}
  </style>
</head>
<body>
@php
  // Sécurisation des variables
  $pharmacy = $pharmacy ?? null;
  $stats    = $stats ?? [];

  $totalOrders   = (int)($stats['total_orders'] ?? 0);
  $validated     = (int)($stats['validated'] ?? 0);
  $rejected      = (int)($stats['rejected'] ?? 0);
  $pending       = (int)($stats['pending'] ?? 0);
  $totalAmount   = (int)($stats['total_amount'] ?? 0);
  $avgAmount     = (float)($stats['avg_amount'] ?? 0);

  $periodLabel   = $stats['period_label'] ?? 'les 30 derniers jours';

  // Calculs de pourcentages basés sur les vrais nombres
  if ($totalOrders > 0) {
      $pValidated = round($validated * 100 / $totalOrders);
      $pRejected  = round($rejected  * 100 / $totalOrders);
      $pPending   = round($pending   * 100 / $totalOrders);
  } else {
      $pValidated = 0;
      $pRejected  = 0;
      $pPending   = 0;
  }
@endphp

<main class="wrap">

  {{-- HEADER --}}
  <div class="top">
    <div class="top-left">
      <h1>Rapports & statistiques</h1>
      <p>
        Synthèse des commandes clients pour
        <strong>{{ $pharmacy->name ?? 'votre pharmacie' }}</strong>
        sur {{ $periodLabel }}.
      </p>
    </div>
    <div class="top-right">
      <span class="pill">
        🏥 {{ $pharmacy->name ?? 'Pharmacie partenaire' }}
      </span>
      <a href="{{ route('pharmacist.dashboard') }}" class="btn btn-sm">
        ← Retour tableau de bord
      </a>
    </div>
  </div>

  <div class="card">

    {{-- FILTRES PÉRIODE --}}
    <div class="filters" aria-label="Filtre de période">
      <a href="{{ route('pharmacist.reports', ['period' => '7']) }}"
         class="filter-pill {{ ($stats['period'] ?? '30') == '7' ? 'active' : '' }}">
        7 jours
      </a>

      <a href="{{ route('pharmacist.reports', ['period' => '30']) }}"
         class="filter-pill {{ ($stats['period'] ?? '30') == '30' ? 'active' : '' }}">
        30 jours
      </a>

      <a href="{{ route('pharmacist.reports', ['period' => '90']) }}"
         class="filter-pill {{ ($stats['period'] ?? '30') == '90' ? 'active' : '' }}">
        3 mois
      </a>
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
      <div class="kpi">
        <div class="kpi-label">Commandes reçues</div>
        <div class="kpi-value">{{ number_format($totalOrders, 0, ',', ' ') }}</div>
        <div class="kpi-sub">Toutes commandes confondues sur la période.</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Validées</div>
        <div class="kpi-value">{{ number_format($validated, 0, ',', ' ') }}</div>
        <div class="kpi-sub">{{ $pValidated }}% des commandes.</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Montant total</div>
        <div class="kpi-value">{{ number_format($totalAmount, 0, ',', ' ') }} F</div>
        <div class="kpi-sub">Somme des montants validés ou en attente.</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Panier moyen</div>
        <div class="kpi-value">
          {{ $avgAmount > 0 ? number_format($avgAmount, 0, ',', ' ') . ' F' : '—' }}
        </div>
        <div class="kpi-sub">Montant moyen par commande.</div>
      </div>
    </div>

    {{-- GRID 2 COLONNES --}}
    <div class="grid">

      {{-- COL GAUCHE : Top produits --}}
      <section>
        <h2 class="section-title">Produits les plus commandés</h2>
        <p class="section-sub">
          Classement des produits les plus fréquents dans les commandes sur {{ $periodLabel }}.
        </p>

        <div class="table-wrap">
          @php
            $topProducts = $topProducts ?? [];
          @endphp

          @if(!empty($topProducts) && count($topProducts))
            <table role="table">
              <thead>
              <tr>
                <th>Produit</th>
                <th style="width:80px">Qté</th>
                <th style="width:120px">Montant</th>
              </tr>
              </thead>
              <tbody>
              @foreach($topProducts as $row)
                @php
                  // on accepte array ou objet
                  $name   = is_array($row) ? ($row['libelle'] ?? $row['name'] ?? '-') : ($row->libelle ?? $row->name ?? '-');
                  $qty    = (int)(is_array($row) ? ($row['qty'] ?? $row['total_qty'] ?? 0) : ($row->qty ?? $row->total_qty ?? 0));
                  $amount = (int)(is_array($row) ? ($row['amount'] ?? 0) : ($row->amount ?? 0));
                @endphp
                <tr>
                  <td>
                    <div>{{ $name }}</div>
                    <div class="muted">
                      {{ $qty }} commande(s) au total.
                    </div>
                  </td>
                  <td>{{ $qty }}</td>
                  <td>{{ number_format($amount, 0, ',', ' ') }} F</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          @else
            <div class="empty">
              <h3>Aucune donnée produit</h3>
              <p>Pas encore assez de commandes pour établir un classement.</p>
            </div>
          @endif
        </div>
      </section>

      {{-- COL DROITE : Répartition des statuts --}}
      <aside>
  <h2 class="section-title">Répartition par statut</h2>
  <p class="section-sub">
    Vue rapide des statuts des commandes sur la période sélectionnée.
  </p>

  <div class="card" style="background:#0d1a36;border-color:#111827;padding:14px;">
    {{-- En attente --}}
    <div class="status-row">
      <div>
        <div>En attente</div>
        <div class="muted">{{ $pending }} commande(s) — {{ $pPending }}%</div>
      </div>
      <span class="status-badge s-wait">⏳</span>
    </div>
    <div class="bar">
      <div class="bar-inner" style="width: {{ $pPending }}%"></div>
    </div>

    {{-- Validées --}}
    <div class="status-row">
      <div>
        <div>Validées</div>
        <div class="muted">{{ $validated }} commande(s) — {{ $pValidated }}%</div>
      </div>
      <span class="status-badge s-ok">✅</span>
    </div>
    <div class="bar">
      <div class="bar-inner"
           style="width: {{ $pValidated }}%;background:linear-gradient(90deg,#22c55e,#4ade80)"></div>
    </div>

    {{-- Rejetées --}}
    <div class="status-row">
      <div>
        <div>Rejetées</div>
        <div class="muted">{{ $rejected }} commande(s) — {{ $pRejected }}%</div>
      </div>
      <span class="status-badge s-ko">❌</span>
    </div>
    <div class="bar">
      <div class="bar-inner"
           style="width: {{ $pRejected }}%;background:linear-gradient(90deg,#ef4444,#f97316)"></div>
    </div>

    @if($totalOrders <= 0)
      <div class="empty">
        <h3>Aucune commande sur la période</h3>
        <p>Les graphiques se mettront à jour dès que des commandes seront reçues.</p>
      </div>
    @endif
  </div>
</aside>
    </div>
  </div>
</main>
</body>
</html>
