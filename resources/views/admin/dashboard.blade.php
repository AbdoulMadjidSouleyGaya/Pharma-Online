<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PharmaOnline — Admin · Tableau de bord</title>

  <style>
    :root{
      --bg:#020617;
      --surface:#020617;
      --surface-soft:#050816;
      --card:#020617;
      --ink:#f9fafb;
      --muted:#9ca3af;
      --accent:#10b981;
      --accent-soft:rgba(16,185,129,.15);
      --ring:rgba(16,185,129,.25);
      --stroke:#111827;
      --stroke2:#020617;
      --shadow:0 30px 60px rgba(0,0,0,.45);
    }

    *{box-sizing:border-box;margin:0;padding:0;}
    html,body{height:100%;}
    body{
      margin:0;
      font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
      background:
        radial-gradient(circle at 0% 0%, #0f172a 0, transparent 55%),
        radial-gradient(circle at 100% 0%, #064e3b 0, transparent 55%),
        radial-gradient(circle at 50% 100%, #1d4ed8 0, transparent 60%),
        linear-gradient(180deg,#020617,#020617 60%,#020617);
      color:var(--ink);
    }

    /* 🧭 TOP BAR */
    .top{
      position:sticky;top:0;z-index:40;
      display:flex;align-items:center;justify-content:space-between;gap:12px;
      padding:10px 18px;
      border-bottom:1px solid rgba(31,41,55,.9);
      background:linear-gradient(180deg,#020617,#020617);
      box-shadow:0 10px 40px rgba(0,0,0,.55);
      backdrop-filter:blur(14px);
    }
    .brand{display:flex;align-items:center;gap:10px;}
    .brand img{
      width:36px;height:36px;object-fit:contain;
      border-radius:12px;
      border:1px solid rgba(148,163,184,.4);
      background:#020617;
      padding:4px;
    }
    .brand .t{line-height:1.15;}
    .brand .t b{
      display:block;
      font-size:16px;
      letter-spacing:.06em;
      text-transform:uppercase;
    }
    .brand .t span{
      font-size:12px;
      color:var(--muted);
    }

    .top .right{
      display:flex;align-items:center;gap:10px;flex-wrap:wrap;
    }

    .chip{
      background:linear-gradient(135deg,#022c22,#052e16);
      border:1px solid rgba(16,185,129,.6);
      color:#bbf7d0;
      padding:7px 10px;
      border-radius:999px;
      font-size:12px;
      display:inline-flex;
      align-items:center;
      gap:6px;
    }
    .chip span{font-size:13px;}

    .btn{
      appearance:none;border:none;cursor:pointer;text-decoration:none;
      display:inline-flex;align-items:center;justify-content:center;gap:8px;
      padding:8px 13px;border-radius:999px;
      font-weight:600;font-size:13px;
      background:#020617;color:#e5e7eb;
      border:1px solid rgba(55,65,81,1);
      transition:
        transform .04s ease,
        background .16s ease,
        box-shadow .16s ease,
        border-color .16s ease;
      white-space:nowrap;
    }
    .btn span.emoji{font-size:14px;}
    .btn:hover{
      background:#0b1220;
      box-shadow:0 8px 22px rgba(0,0,0,.5);
      border-color:#1f2937;
      transform:translateY(-1px);
    }
    .btn:active{transform:translateY(0);box-shadow:none;}

    .btn.outline{
      background:transparent;
      border-color:rgba(156,163,175,.7);
      color:#e5e7eb;
    }
    .btn.outline:hover{
      background:rgba(15,23,42,.9);
      border-color:#e5e7eb;
    }

    .btn.primary{
      background:linear-gradient(135deg,#10b981,#22c55e);
      border-color:transparent;
      color:#022c22;
    }
    .btn.primary:hover{
      filter:brightness(1.06);
      box-shadow:0 10px 30px rgba(16,185,129,.45);
    }

    .wrap{
      max-width:1200px;
      margin:22px auto 26px;
      padding:0 18px 32px;
    }

    /* 🧱 LAYOUT & CARDS */
    .page-head{
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap:10px;
      margin-bottom:18px;
    }
    .page-title-block{
      display:flex;
      flex-direction:column;
      gap:4px;
    }
    .page-title{
      font-size:24px;
      font-weight:700;
    }
    .page-sub{
      color:var(--muted);
      font-size:13px;
    }
    .page-tag{
      font-size:11px;
      padding:4px 10px;
      border-radius:999px;
      border:1px solid rgba(55,65,81,1);
      color:var(--muted);
      background:rgba(15,23,42,.85);
    }

    .grid{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:14px;
    }
    @media (max-width:1050px){
      .grid{grid-template-columns:repeat(2,minmax(0,1fr));}
    }
    @media (max-width:640px){
      .grid{grid-template-columns:1fr;}
    }

    .card{
      background:radial-gradient(circle at 0% 0%,rgba(16,185,129,.18),transparent 55%),
                 linear-gradient(180deg,#020617,#020617);
      border:1px solid rgba(31,41,55,1);
      border-radius:18px;
      padding:16px;
      box-shadow:var(--shadow);
    }

    .card-soft{
      background:radial-gradient(circle at 0 0,rgba(59,130,246,.16),transparent 55%),
                 linear-gradient(180deg,#020617,#020617);
      border:1px solid rgba(31,41,55,1);
      border-radius:18px;
      padding:16px;
      box-shadow:var(--shadow);
    }

    .stat{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:10px;
    }
    .stat .k{
      font-size:12px;
      color:var(--muted);
      text-transform:uppercase;
      letter-spacing:.06em;
    }
    .stat .v{
      font-size:28px;
      font-weight:800;
      margin-top:2px;
    }
    .pill{
      display:inline-flex;
      align-items:center;
      gap:6px;
      font-size:11px;
      padding:4px 9px;
      border-radius:999px;
      border:1px solid rgba(31,41,55,1);
      background:rgba(15,23,42,1);
      color:#a7f3d0;
      margin-top:8px;
    }
    .pill span.emoji{font-size:13px;}

    .muted{color:var(--muted);font-size:13px;}

    .cols{
      display:grid;
      grid-template-columns:2fr 1fr;
      gap:14px;
      margin-top:18px;
    }
    @media (max-width:980px){
      .cols{grid-template-columns:1fr;}
    }

    table{
      width:100%;
      border-collapse:separate;
      border-spacing:0;
      border-radius:14px;
      overflow:hidden;
      border:1px solid rgba(31,41,55,1);
    }
    th,td{
      padding:9px 12px;
      text-align:left;
      font-size:13px;
    }
    thead th{
      background:#020617;
      color:#c7d2fe;
      border-bottom:1px solid rgba(31,41,55,1);
    }
    tbody tr{
      background:#020617;
      border-bottom:1px solid rgba(31,41,55,1);
    }
    tbody tr:nth-child(even){
      background:#020617;
    }
    tbody tr:hover td{
      background:#020617;
      border-bottom-color:rgba(37,99,235,.6);
    }
    tbody td small{color:var(--muted);}

    .table-empty{
      padding:12px;
      text-align:center;
      font-size:13px;
      color:var(--muted);
    }

    .actions{
      display:grid;
      gap:10px;
    }
    .actions .btn{
      width:100%;
      justify-content:flex-start;
    }

    .sep{
      height:1px;
      background:rgba(31,41,55,1);
      margin:12px 0 6px;
    }

    .footer{
      margin:20px 0 30px;
      color:#6b7280;
      font-size:12px;
      text-align:center;
    }
  </style>
</head>
<body>

  {{-- TOP BAR --}}
  <div class="top">
    <div class="brand">
      <img src="{{ asset('images/logo.png') }}" alt="PharmaOnline">
      <div class="t">
        <b>PharmaOnline</b>
        <span>Interface administrateur</span>
      </div>
    </div>
    <div class="right">
      <div class="chip">
        <span>Connecté : {{ auth()->user()->name }}</span>
      </div>
      <a class="btn outline" href="{{ route('profile.edit') }}">
        <span>Profil</span>
      </a>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn" type="submit">
          <span>Se déconnecter</span>
        </button>
      </form>
    </div>
  </div>

  {{-- PHP côté vue pour rester compatible même si certaines tables n’existent pas encore --}}
@php
  use Illuminate\Support\Facades\Schema;

  $usersCount = \App\Models\User::count();

  $pharmaciesCount = (class_exists(\App\Models\Pharmacy::class) && Schema::hasTable('pharmacies'))
    ? \App\Models\Pharmacy::count() : 0;

  $productsCount = (class_exists(\App\Models\Product::class) && Schema::hasTable('products'))
    ? \App\Models\Product::count() : 0;

  // ✅ IMPORTANT : ne plus écraser $ordersCount venant du controller
  // (optionnel) si jamais le controller n’envoie pas, on met un fallback
  if (!isset($ordersCount)) {
      $ordersCount = (class_exists(\App\Models\CustomerOrder::class) && Schema::hasTable('customer_orders'))
          ? \App\Models\CustomerOrder::where('status', '!=', 'annulee')->count()
          : 0;
  }

  $recentUsers = \App\Models\User::latest()->take(5)->get(['name','email','created_at']);
@endphp


  <div class="wrap">

    {{-- En-tête page --}}
    <div class="page-head">
      <div class="page-title-block">
        <div class="page-title">Tableau de bord administrateur</div>
        <div class="page-sub">
          Vue globale sur les comptes, les pharmacies et les ressources clés de la plateforme.
        </div>
      </div>
      <div class="page-tag">
        Aujourd’hui · {{ now()->format('d/m/Y') }}
      </div>
    </div>

    {{-- STATS PRINCIPALES --}}
    <div class="grid">

      <div class="card stat">
        <div>
          <div class="k">Utilisateurs</div>
          <div class="v">{{ number_format($usersCount, 0, ',', ' ') }}</div>
          <div class="pill">
            <span>Comptes enregistrés</span>
          </div>
        </div>
        <div class="muted">Gestion globale des accès</div>
      </div>

      <div class="card stat">
        <div>
          <div class="k">Pharmacies</div>
          <div class="v">{{ number_format($pharmaciesCount, 0, ',', ' ') }}</div>
          <div class="pill">
            <span>Pharmacies intégrées</span>
          </div>
        </div>
        <div class="muted">Couverture du réseau PharmaOnline</div>
      </div>

      <div class="card stat">
      <div>
        <div class="k">Commandes</div>
        <div class="v">{{ number_format($ordersCount ?? 0) }}</div>
        <div class="pill">
          <span>Commandes envoyées</span>
        </div>
      </div>
      <div class="muted">Total des commandes créées par les utilisateurs</div>
    </div>
      <div class="card stat">
        <div>
          <div class="k">Commandes</div>
          <div class="v">{{ number_format($ordersCount, 0, ',', ' ') }}</div>
          <div class="pill">
            <span>Commandes enregistrées</span>
          </div>
        </div>
        <div class="muted">Suivi des flux patients / clients</div>
      </div>

    </div>

    {{-- COLONNES --}}
    <div class="cols">

      {{-- Derniers utilisateurs --}}
      <div class="card-soft">
        <h3 style="margin:0 0 6px;font-size:15px;">Derniers inscrits</h3>
        <div class="muted" style="margin-bottom:10px">
          Aperçu des 5 derniers comptes créés sur la plateforme.
        </div>

        <table aria-label="Derniers utilisateurs">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Créé le</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentUsers as $u)
              <tr>
                <td>{{ $u->name }}</td>
                <td><small>{{ $u->email }}</small></td>
                <td><small>{{ $u->created_at->format('d/m/Y H:i') }}</small></td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="table-empty">
                  Aucun utilisateur pour le moment.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="sep"></div>
        <div class="muted">
          Ces informations permettent de vérifier rapidement les nouveaux comptes créés.
        </div>
      </div>

      {{-- Actions rapides --}}
      <div class="card-soft">
        <h3 style="margin:0 0 6px;font-size:15px;">Actions rapides</h3>
        <div class="muted" style="margin-bottom:10px">
          Accède directement aux sections clés de l’administration.
        </div>

        <div class="actions">
          <a href="{{ route('admin.users.index') }}" class="btn primary">
            <span>Gérer les utilisateurs</span>
          </a>
          <a href="{{ route('admin.pharmacies.index') }}" class="btn">
            <span>Gérer les pharmacies (admin)</span>
          </a>
          <a href="{{ route('admin.guards.index') }}" class="btn">
            <span>Pharmacies de garde</span>
          </a>
          <a href="{{ route('admin.pharmacists.index') }}" class="btn">
            <span>Gérer les pharmaciens</span>
          </a>
        </div>

        <div class="sep"></div>
        <div class="muted">
          Tu peux enrichir cette zone plus tard avec d’autres liens (statistiques détaillées, logs, etc.).
        </div>
      </div>

    </div>

    <div class="footer">
      © {{ date('Y') }} PharmaOnline — Interface administrateur · Tous droits réservés
    </div>
  </div>
</body>
</html>
