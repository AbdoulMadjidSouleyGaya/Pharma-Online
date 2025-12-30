<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Tableau de bord — PharmaOnline</title>
  <style>
    :root {
      --brand:#2563eb; --brand2:#1d4ed8; --accent:#0ea5e9; --ok:#10b981;
      --bg:#f8fafc; --ink:#0b1220; --muted:#475569;
      --panel:#ffffff; --border:#e5e7eb;
      --radius:18px; --shadow:0 10px 24px rgba(2,6,23,.08);
    }
    *{box-sizing:border-box;}
    html,body{
      margin:0; color:var(--ink);
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:
        radial-gradient(900px 520px at -20% -10%, #dbeafe60, transparent 60%),
        radial-gradient(900px 520px at 120% -10%, #bbf7d060, transparent 60%),
        var(--bg);
    }

    /* Topbar */
    .topbar{
      position:sticky;top:0;z-index:50;
      backdrop-filter:saturate(160%) blur(10px);
      background:linear-gradient(100deg,#ffffffee,#f8fbffdd);
      border-bottom:1px solid #e6eefc;
    }
    .top-in{
      max-width:1200px;margin:0 auto;
      padding:12px 16px;
      display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;
    }
    .brand-link{display:flex;align-items:center;gap:10px;text-decoration:none;}
    .logo{
      width:42px;height:42px;border-radius:12px;
      background:conic-gradient(from 180deg at 50% 50%, #34d399, #10b981, #2563eb, #60a5fa);
      display:grid;place-items:center;color:#fff;font-weight:900;
      box-shadow:0 6px 14px rgba(37,99,235,.25);
    }
    .brand-title{font-weight:900;letter-spacing:.3px;color:#0b1220;font-size:1.2rem;}
    .right{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
    .btn{
      border:1px solid #cdd6e3;background:#fff;border-radius:10px;
      padding:9px 14px;font-weight:700;text-decoration:none;color:#0b1220;cursor:pointer;
      display:inline-flex;align-items:center;gap:6px;transition:.2s;font-size:.9rem;
    }
    .btn:hover{transform:translateY(-1px);box-shadow:0 8px 16px rgba(2,6,23,.10);}
    .btn.primary{background:linear-gradient(135deg, var(--brand2), var(--brand));color:#fff;}
    .btn.ok{background:linear-gradient(135deg,#0ea5e9,#10b981);color:#fff;}
    .btn.outline{background:#f8fafc;border-color:#dbe3ef;}
    .btn.outline:hover{background:#f1f5f9;}
    @media(max-width:700px){ .right{justify-content:center;width:100%;} }

    /* Wrapper */
    .wrap{max-width:1200px;margin:28px auto;padding:0 16px;}

    /* Hero */
    .hero{
      border-radius:var(--radius);
      background:linear-gradient(135deg, #ffffff, #eff6ff);
      border:1px solid var(--border);
      box-shadow:var(--shadow);
      padding:30px;
      display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;
    }
    .hero h1{margin:0;font-size:30px;font-weight:900;color:#0b1220;}
    .hero p{margin:6px 0 0;color:#475569;}
    .chip{background:#e0f2fe;color:#1e40af;font-weight:700;border-radius:999px;padding:6px 12px;font-size:.85rem;}

    /* Stats */
    .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;margin-top:24px;}
    .stat{
      background:var(--panel);border:1px solid var(--border);border-radius:14px;
      padding:18px 20px;box-shadow:var(--shadow);display:flex;flex-direction:column;gap:4px;
      transition:.15s;}
    .stat:hover{transform:translateY(-2px);box-shadow:0 14px 24px rgba(2,6,23,.1);}
    .stat h2{font-size:14px;color:#64748b;margin:0;}
    .stat p{font-size:24px;font-weight:900;margin:0;color:#0b1220;}

    /* Cards */
    .grid{margin-top:28px;display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;}
    .card{
      background:var(--panel);border:1px solid var(--border);border-radius:18px;
      padding:24px;box-shadow:var(--shadow);
      display:flex;flex-direction:column;gap:12px;transition:.2s;}
    .card:hover{transform:translateY(-3px);box-shadow:0 16px 32px rgba(2,6,23,.12);}
    .title{font-weight:900;font-size:20px;color:#0b1220;display:flex;align-items:center;gap:10px;}
    .card p{margin:0;color:var(--muted);}
    .cta{margin-top:auto;display:flex;gap:10px;flex-wrap:wrap;}
    .hint{font-size:12px;color:#64748b;}

    footer{margin-top:40px;text-align:center;color:#64748b;font-size:14px;padding:20px;}
  </style>
</head>
<body>

<header class="topbar">
  <div class="top-in">
    <a class="brand-link" href="{{ route('dashboard') }}">
      <div class="logo">Rx</div>
      <div class="brand-title">PharmaOnline</div>
    </a>

    <div class="right">
      <a href="{{ route('profile.edit') }}" class="btn outline" aria-label="Mon profil">👤 Mon profil</a>
      <a href="{{ route('contact') }}" class="btn ok" aria-label="Support & Assistance">💬 Support</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn outline" type="submit">🚪 Déconnexion</button>
      </form>
    </div>
  </div>
</header>

<main class="wrap">
  <section class="hero">
    <div>
      <h1>Bienvenue, {{ Auth::user()->name }} 👋</h1>
      <p>Votre espace personnel pour tout gérer facilement.</p>
      <div style="margin-top:10px;">
        <span class="chip">Accès Premium</span>
        <span class="chip">Sécurisé</span>
      </div>
    </div>
  </section>

  @php
    $orders = collect(session('orders', []))->values();
    $totalOrders = $orders->count();
    $validated   = $orders->where('status','valide')->count();
    $totalAmount = (int) $orders->sum('total');
  @endphp

  <section class="stats">
    <div class="stat">
      <h2>Commandes passées</h2>
      <p>{{ $totalOrders }}</p>
    </div>
    <div class="stat">
      <h2>Commandes validées</h2>
      <p>{{ $validated }}</p>
    </div>
    <div class="stat">
      <h2>Total estimé</h2>
      <p>{{ number_format($totalAmount, 0, ',', ' ') }} F</p>
    </div>
  </section>

  <section class="grid">
    <article class="card">
      <div class="title">🛟 Pharmacies de garde</div>
      <p>Consultez les pharmacies ouvertes autour de vous, semaine en cours ou à venir.</p>
      <div class="cta">
        <a class="btn primary" href="{{ route('guards.public') }}">Voir la liste</a>
        <span class="hint">Actualisées chaque semaine</span>
      </div>
    </article>

    <article class="card">
      <div class="title">💊 Produits & Commande</div>
      <p>Recherchez des produits, comparez les prix et envoyez vos commandes.</p>
      <div class="cta">
      <a class="btn ok" href="{{ route('produits.commande.pharmacies') }}">Rechercher</a>
        <span class="hint">Produits disponibles uniquement</span>
      </div>
    </article>

    <article class="card">
      <div class="title">📦 Mes commandes</div>
      <p>Accédez à l’historique et suivez le statut de vos commandes.</p>
      <div class="cta">
        <a class="btn"style="background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border-color:#f59e0b;"href="{{ route('orders.index') }}">Mes commandes</a>
        <span class="hint">Suivi complet en temps réel</span>
      </div>
    </article>
  </section>

  <footer>
    © {{ date('Y') }} PharmaOnline — Votre santé connectée 💚
  </footer>
</main>

</body>
</html>