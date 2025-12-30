<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Console — Gestion Pharmacien</title>

  <style>
    :root{
      --blue:#2563eb; --blue2:#1d4ed8; --blue3:#60a5fa;
      --bg1:#0f172a; --bg2:#1e293b;
      --ink:#e5e7eb; --muted:#94a3b8;
      --radius:16px; --shadow:0 20px 45px rgba(0,0,0,.25);
    }

    *{box-sizing:border-box}

    /* 🎨 Nouveau fond ultra propre + dégradé + particules */
    body{
      margin:0;
      min-height:100vh;
      background:
        radial-gradient(circle at 20% 15%, #1e3a8a55, transparent 70%),
        radial-gradient(circle at 80% 0%, #0ea5e955, transparent 70%),
        radial-gradient(circle at 50% 100%, #10b98133, transparent 70%),
        linear-gradient(180deg, var(--bg1), var(--bg2));
      background-attachment: fixed;
      font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
      color:var(--ink);
    }

    /* Topbar */
    .topbar{
      position:sticky;top:0;z-index:40;
      background:rgba(15,23,42,0.7);
      backdrop-filter:blur(18px);
      border-bottom:1px solid rgba(255,255,255,.06);
    }
    .topbar-in{
      max-width:1180px;margin:0 auto;
      padding:14px 18px;
      display:flex;align-items:center;justify-content:space-between;
    }

    /* Branding */
    .brand{display:flex;align-items:center;gap:10px}
    .brand-logo{
      width:36px;height:36px;border-radius:14px;
      background:radial-gradient(circle at 30% 20%, #38bdf8 0%, #0f172a 60%);
      border:1px solid rgba(255,255,255,.25);
      display:grid;place-items:center;font-size:18px;
    }
    .brand-title{font-weight:700;font-size:15px}
    .brand-sub{font-size:12px;color:rgba(255,255,255,.45);line-height:1.2}

    /* Buttons */
    .btn{
      border:1px solid rgba(255,255,255,.1);
      background:rgba(255,255,255,.06);
      color:#fff;padding:7px 13px;border-radius:999px;
      font-size:13px;font-weight:500;
      display:inline-flex;align-items:center;gap:6px;
      cursor:pointer;transition:.12s;
      text-decoration:none;
    }
    .btn:hover{background:rgba(255,255,255,.15);transform:translateY(-1px)}
    .wrap{max-width:1180px;margin:24px auto;padding:0 18px}

    /* Cards */
    .card{
      background:rgba(255,255,255,0.06);
      border:1px solid rgba(255,255,255,.08);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      padding:18px;
      backdrop-filter:blur(6px);
    }
    .card-title{font-weight:600;margin-bottom:4px}
    .card-sub{color:var(--muted);font-size:13px;margin-bottom:16px}

    /* Quick tools */
    .quick-grid{
      display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;
    }
    .quick-item{
      background:rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.1);
      border-radius:14px;padding:14px;
      display:flex;flex-direction:column;gap:7px;
      backdrop-filter:blur(3px);
      transition:.15s;
    }
    .quick-item:hover{transform:translateY(-3px);background:rgba(255,255,255,.1)}

    .quick-title{font-weight:600;font-size:14px}
    .quick-meta{font-size:12px;color:var(--muted)}
    .quick-footer{margin-top:auto;display:flex;justify-content:space-between;align-items:center}
    .pill{
      background:rgba(37,99,235,.25);
      border:1px solid rgba(37,99,235,.4);
      color:#dbeafe;font-size:11px;padding:2px 8px;border-radius:9999px;
    }

    /* Monitoring */
    .list-micro{display:flex;flex-direction:column;gap:10px}
    .list-item{
      display:flex;justify-content:space-between;align-items:center;
      background:rgba(255,255,255,.05);
      border:1px solid rgba(255,255,255,.04);
      border-radius:14px;padding:12px 14px;
    }
    .list-label{font-weight:500;font-size:14px}
    .list-sub{color:var(--muted);font-size:12px}
    .status-tag{
      font-size:11px;background:rgba(22,163,74,.18);
      color:#86efac;border:1px solid rgba(22,163,74,.4);
      padding:3px 9px;border-radius:999px;
    }
    .danger-tag{
      font-size:11px;background:rgba(248,113,113,.18);
      color:#fecaca;border:1px solid rgba(248,113,113,.4);
      padding:3px 9px;border-radius:999px;
    }

    /* Layout simple pour la colonne principale */
    .layout{
      display:flex;
      flex-direction:column;
      gap:16px;
    }
    .col-main{
      flex:1;
    }
  </style>
</head>
<body>

<header class="topbar">
  <div class="topbar-in">
    <div class="brand">
      <div class="brand-logo">⚙️</div>
      <div>
        <div class="brand-title">Espace Pharmacien</div>
        <div class="brand-sub">
          Console de gestion • PharmaOnline<br>
          Connecté en tant que <strong>{{ Auth::user()->name }}</strong>
        </div>
      </div>
    </div>

    @php
        $user = Auth::user();
        $unreadStockAlerts = \App\Models\StockAlert::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    @endphp

    <div class="top-actions" style="
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:nowrap;
    ">

      {{-- 🔔 Notifications de stock --}}
      <a class="btn" href="{{ route('pharmacist.stock.alerts') }}">
        🔔 Notifications
        @if($unreadStockAlerts > 0)
          <span style="
            background:#ef4444;
            color:white;
            border-radius:999px;
            padding:1px 7px;
            font-size:11px;
            font-weight:700;
          ">
            {{ $unreadStockAlerts }}
          </span>
        @endif
      </a>

      <a class="btn" href="{{ route('pharmacist.dashboard') }}">⬅️ Tableau de bord</a>
      <a class="btn" href="{{ route('profile.edit') }}">👤 Mon profil</a>

      <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button class="btn" type="submit">Quitter</button>
      </form>
    </div>

  </div>
</header>

<div class="wrap">

  <h1 class="page-title" style="font-size:26px;font-weight:700;margin-bottom:6px;">
    Centre de gestion
  </h1>
  <div class="page-sub" style="color:var(--muted);font-size:14px;margin-bottom:20px;">
    Accès rapide aux outils du pharmacien, API AbdouPharma, gestion de stock et suivi des activités.
  </div>

  <div class="layout">

    <!-- Colonne principale -->
    <div class="col-main">
      <div class="card">
        <div class="card-title">Outils rapides</div>
        <div class="card-sub">Accédez rapidement aux fonctionnalités essentielles.</div>

        <div class="quick-grid">

          <!-- Gestion API (inchangé) -->
          <div class="quick-item">
            <div class="quick-title">Gestion API</div>
            <div class="quick-meta">Configurer l’API AbdouPharma.</div>
            <div class="quick-footer">
              <a class="btn" href="{{ url('/pharmacist/api') }}">Ouvrir</a>
              <span class="pill">API</span>
            </div>
          </div>

          <!-- 🔥 Gestion de stock -->
          <div class="quick-item">
            <div class="quick-title">Gestion de stock</div>
            <div class="quick-meta">Surveiller les niveaux, entrées & sorties.</div>
            <div class="quick-footer">
              <a class="btn" href="{{ route('pharmacist.stock.index') }}">Ouvrir</a>
              <span class="pill">Stock</span>
            </div>
          </div>

          <div class="quick-item">
            <div class="quick-title">Produits synchronisés</div>
            <div class="quick-meta">Voir l’inventaire de votre pharmacie.</div>
            <div class="quick-footer">
              <a class="btn" href="{{ url('/pharmacist/api/products') }}">Ouvrir</a>
              <span class="pill">Stocks</span>
            </div>
          </div>

          <div class="quick-item">
            <div class="quick-title">Commandes reçues</div>
            <div class="quick-meta">Suivi des commandes clients.</div>
            <div class="quick-footer">
              <a class="btn" href="{{ url('/pharmacist/orders') }}">Ouvrir</a>
              <span class="pill">Live</span>
            </div>
          </div>

          <div class="quick-item">
            <div class="quick-title">Rapports & Exports</div>
            <div class="quick-meta">Statistiques, ventes, CSV.</div>
            <div class="quick-footer">
              <a class="btn" href="{{ url('/pharmacist/reports') }}">Ouvrir</a>
              <span class="pill">CSV</span>
            </div>
          </div>

        </div>
      </div>

      <div style="height:16px"></div>

      <!-- Activité récente -->
      <div class="card">
        <div class="card-title">Activité récente</div>
        <div class="card-sub">Derniers événements automatiques côté serveur.</div>

        <div class="list-micro">

          <div class="list-item">
            <div>
              <div class="list-label">Synchronisation AbdouPharma</div>
              <div class="list-sub">Exécution réussie.</div>
            </div>
            <span class="status-tag">OK</span>
          </div>

          <div class="list-item">
            <div>
              <div class="list-label">Mise à jour de l'API</div>
              <div class="list-sub"> Exécution réussie </div>
            </div>
            <span class="status-tag">OK</span>
          </div>

        </div>
      </div>

    </div>

  </div>
</div>

</body>
</html>
