<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Mon profil - PharmaOnline</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --bg:#020617;
      --bg2:#0b1220;
      --panel:rgba(15,23,42,.98);
      --border:#1f2937;
      --accent:#22c55e;
      --accent-soft:#16a34a;
      --accent-blue:#3b82f6;
      --ink:#e5e7eb;
      --muted:#9ca3af;
      --danger:#ef4444;
    }

    *{box-sizing:border-box}
    html,body{
      margin:0;
      min-height:100vh;
      background:
        radial-gradient(circle at top, rgba(34,197,94,.18), transparent 55%),
        radial-gradient(circle at bottom, rgba(37,99,235,.18), transparent 60%),
        var(--bg);
      color:var(--ink);
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
    }
    a{color:inherit;text-decoration:none}

    .shell{
      min-height:100vh;
      display:flex;
      flex-direction:column;
    }

    .topbar{
      position:sticky;
      top:0;
      z-index:10;
      background:linear-gradient(120deg,#0f172a 0%, #111827 45%, #1e293b 100%);
      border-bottom:1px solid rgba(15,23,42,.9);
      box-shadow:0 10px 40px rgba(15,23,42,.9);
    }
    .topbar-in{
      max-width:1120px;
      margin:0 auto;
      padding:10px 16px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    }
    .brand{
      display:flex;
      align-items:center;
      gap:10px;
    }
    .brand-logo{
      width:32px;height:32px;
      border-radius:12px;
      background:radial-gradient(circle at 30% 0, #4ade80, #16a34a);
      box-shadow:
        0 0 0 1px #bbf7d0,
        0 12px 30px rgba(16,185,129,.7);
      display:grid;
      place-items:center;
      font-weight:800;
      color:#022c22;
      font-size:15px;
    }
    .brand-text{
      display:flex;
      flex-direction:column;
      line-height:1.1;
    }
    .brand-title{
      font-size:15px;
      font-weight:700;
      letter-spacing:.08em;
      text-transform:uppercase;
      color:#e5e7eb;
    }
    .brand-sub{
      font-size:11px;
      color:#9ca3af;
    }

    .top-actions{
      display:flex;
      align-items:center;
      gap:8px;
      flex-wrap:wrap;
      font-size:12px;
      color:#cbd5f5;
    }
    .top-actions span.name{
      font-weight:600;
      max-width:180px;
      text-overflow:ellipsis;
      white-space:nowrap;
      overflow:hidden;
    }

    .btn{
      appearance:none;
      border-radius:999px;
      padding:8px 14px;
      font-size:12px;
      font-weight:600;
      border:1px solid rgba(148,163,184,.6);
      background:rgba(15,23,42,.8);
      color:#e5e7eb;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      gap:6px;
      transition:background .15s ease, border-color .15s ease, transform .08s ease, box-shadow .15s ease;
    }
    .btn:hover{
      transform:translateY(-1px);
      border-color:#e5e7eb;
      box-shadow:0 10px 30px rgba(15,23,42,.9);
    }
    .btn-primary{
      border-color:#22c55e;
      background:linear-gradient(135deg,#22c55e,#16a34a);
      color:#022c22;
      box-shadow:0 14px 40px rgba(22,163,74,.75);
    }
    .btn-primary:hover{
      filter:brightness(1.06);
      box-shadow:0 18px 50px rgba(22,163,74,.9);
    }
    .btn-ghost{
      background:transparent;
      border-color:rgba(148,163,184,.5);
    }
    .btn-danger{
      border-color:#b91c1c;
      background:rgba(127,29,29,.95);
      color:#fee2e2;
    }
    .btn-danger:hover{
      border-color:#fca5a5;
      box-shadow:0 12px 35px rgba(185,28,28,.9);
    }

    .wrap{
      flex:1;
      max-width:1120px;
      margin:0 auto;
      padding:20px 16px 32px;
    }

    .page-title{
      margin:0 0 4px;
      font-size:22px;
      font-weight:800;
      letter-spacing:.02em;
    }
    .page-subtitle{
      margin:0;
      font-size:13px;
      color:var(--muted);
    }

    .layout{
      display:grid;
      grid-template-columns:minmax(0,2fr) minmax(260px,1.3fr);
      gap:18px;
      margin-top:20px;
    }

    .card{
      background:var(--panel);
      border-radius:20px;
      border:1px solid rgba(30,64,175,.7);
      box-shadow:
        0 24px 80px rgba(15,23,42,.95),
        0 0 0 1px rgba(15,23,42,1);
      padding:18px 18px 16px;
      position:relative;
      overflow:hidden;
    }
    .card::before{
      content:"";
      position:absolute;
      inset:-40%;
      background:
        radial-gradient(circle at top, rgba(34,197,94,.22), transparent 60%),
        radial-gradient(circle at bottom right, rgba(59,130,246,.16), transparent 60%);
      opacity:.7;
      pointer-events:none;
    }
    .card-inner{
      position:relative;
      z-index:1;
    }

    .card-header{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:10px;
      margin-bottom:12px;
    }
    .card-title{
      font-size:15px;
      font-weight:700;
      letter-spacing:.04em;
      text-transform:uppercase;
      color:#e5e7eb;
    }
    .card-sub{
      margin-top:4px;
      font-size:12px;
      color:var(--muted);
    }

    .badge-soft{
      padding:4px 9px;
      border-radius:999px;
      font-size:11px;
      border:1px solid rgba(148,163,184,.5);
      background:rgba(15,23,42,.9);
      color:var(--muted);
      display:inline-flex;
      align-items:center;
      gap:6px;
    }
    .badge-soft span.dot{
      width:7px;height:7px;border-radius:999px;
      background:#22c55e;
      box-shadow:0 0 10px rgba(34,197,94,.9);
    }

    .form-grid{
      display:grid;
      grid-template-columns:1fr;
      gap:12px;
      margin-top:6px;
    }
    .group-label{
      font-size:12px;
      font-weight:600;
      color:#e5e7eb;
      margin-bottom:2px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:8px;
    }
    .group-label span.help{
      font-size:11px;
      color:var(--muted);
      font-weight:400;
    }
    .field{
      width:100%;
      border-radius:12px;
      border:1px solid rgba(51,65,85,.9);
      background:rgba(15,23,42,.9);
      color:var(--ink);
      font-size:13px;
      padding:9px 11px;
      outline:none;
      transition:border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .field:focus{
      border-color:#22c55e;
      box-shadow:0 0 0 1px rgba(34,197,94,.5), 0 0 0 4px rgba(34,197,94,.15);
      background:rgba(15,23,42,1);
    }

    .hint-inline{
      font-size:11px;
      color:var(--muted);
      margin-top:3px;
    }

    .card-footer{
      margin-top:14px;
      display:flex;
      justify-content:flex-end;
      gap:8px;
      flex-wrap:wrap;
    }

    .status-box{
      margin-top:8px;
      font-size:12px;
    }
    .status-ok{
      border-radius:10px;
      padding:7px 9px;
      border:1px solid rgba(22,163,74,.7);
      background:rgba(6,95,70,.9);
      color:#bbf7d0;
    }
    .status-error{
      border-radius:10px;
      padding:7px 9px;
      border:1px solid rgba(248,113,113,.8);
      background:rgba(69,10,10,.96);
      color:#fecaca;
    }

    .errors-list{
      margin:0;
      padding-left:18px;
      font-size:12px;
    }

    .danger-card{
      border-color:rgba(185,28,28,.85);
      background:rgba(15,23,42,.98);
    }
    .danger-card::before{
      background:
        radial-gradient(circle at top, rgba(239,68,68,.25), transparent 60%);
    }
    .danger-text{
      font-size:12px;
      color:#fecaca;
      margin-top:4px;
    }

    .danger-note{
      margin-top:8px;
      padding:7px 9px;
      border-radius:10px;
      border:1px dashed rgba(248,113,113,.7);
      background:rgba(127,29,29,.7);
      font-size:11px;
      color:#fee2e2;
    }

    hr.section-sep{
      border:none;
      border-top:1px solid rgba(30,64,175,.8);
      margin:18px 0 10px;
    }

    @media(max-width:900px){
      .layout{
        grid-template-columns:1fr;
      }
    }
  </style>
</head>
<body>
<div class="shell">

  <!-- Topbar -->
  <header class="topbar">
    <div class="topbar-in">
      <div class="brand">
        <div class="brand-logo">Rx</div>
        <div class="brand-text">
          <div class="brand-title">PharmaOnline</div>
          <div class="brand-sub">Espace utilisateur · Profil</div>
        </div>
      </div>

      <div class="top-actions">
        <span class="name">👤 {{ auth()->user()->name }}</span>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">
          ← Retour au tableau de bord
        </a>
      </div>
    </div>
  </header>

  <main class="wrap">
    <header style="margin-bottom:12px;">
      <h1 class="page-title">Mon profil</h1>
      <p class="page-subtitle">
        Mettez à jour vos informations personnelles et sécurisez l’accès à votre compte.
      </p>
    </header>

    {{-- Messages globaux --}}
    @if (session('status'))
      <div class="status-box">
        <div class="status-ok">
          ✅ {{ session('status') }}
        </div>
      </div>
    @endif

    @if ($errors->any())
      <div class="status-box" style="margin-top:10px;">
        <div class="status-error">
          ⚠️ Certaines informations sont invalides :
          <ul class="errors-list">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <div class="layout">

      {{-- Colonne gauche : Infos + mot de passe --}}
      <section class="card">
        <div class="card-inner">
          <div class="card-header">
            <div>
              <div class="card-title">Informations du compte</div>
              <p class="card-sub">
                Ces informations sont utilisées pour votre authentification et vos échanges avec PharmaOnline.
              </p>
            </div>
            <div class="badge-soft">
              <span class="dot"></span>
              Profil actif
            </div>
          </div>

          {{-- Formulaire infos générales --}}
          <form method="POST" action="{{ route('profile.update') }}" class="form-grid">
            @csrf
            @method('PATCH')

            {{-- Nom --}}
            <div>
              <div class="group-label">
                <span>Nom complet</span>
                <span class="help">Comme il apparaît sur vos commandes.</span>
              </div>
              <input
                id="name"
                name="name"
                type="text"
                class="field"
                value="{{ old('name', $user->name) }}"
                required
                autocomplete="name"
              >
            </div>

            {{-- Email --}}
            <div>
              <div class="group-label">
                <span>Adresse e-mail</span>
                <span class="help">Utilisée pour la connexion et les notifications.</span>
              </div>
              <input
                id="email"
                name="email"
                type="email"
                class="field"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="email"
              >
              <p class="hint-inline">
                Assurez-vous d’utiliser une adresse à laquelle vous avez toujours accès.
              </p>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary">
                💾 Enregistrer les modifications
              </button>
            </div>
          </form>

          <hr class="section-sep">

          {{-- Bloc sécurité & mot de passe --}}
          <div class="card-header" style="margin-top:0;">
            <div>
              <div class="card-title">Sécurité & mot de passe</div>
              <p class="card-sub">
                Modifiez régulièrement votre mot de passe pour protéger votre compte.
              </p>
            </div>
          </div>

          <form method="POST" action="{{ route('profile.password.update') }}" class="form-grid">
            @csrf
            @method('PUT')

            <div>
              <div class="group-label">
                <span>Mot de passe actuel</span>
                <span class="help">Obligatoire pour confirmer le changement.</span>
              </div>
              <input
                id="current_password"
                name="current_password"
                type="password"
                class="field"
                autocomplete="current-password"
                required
              >
            </div>

            <div>
              <div class="group-label">
                <span>Nouveau mot de passe</span>
                <span class="help">Au moins 8 caractères.</span>
              </div>
              <input
                id="password"
                name="password"
                type="password"
                class="field"
                autocomplete="new-password"
                required
              >
            </div>

            <div>
              <div class="group-label">
                <span>Confirmer le mot de passe</span>
                <span class="help">Doit être identique au nouveau.</span>
              </div>
              <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="field"
                autocomplete="new-password"
                required
              >
            </div>

            <div class="card-footer" style="justify-content:flex-start;">
              <button type="submit" class="btn btn-primary">
                🔒 Mettre à jour le mot de passe
              </button>
            </div>
          </form>
        </div>
      </section>

      {{-- Colonne droite : zone sensible --}}
      <aside class="card danger-card">
        <div class="card-inner">
          <div class="card-header">
            <div>
              <div class="card-title">Zone sensible</div>
              <p class="card-sub">
                Actions irréversibles. À utiliser uniquement si vous êtes sûr de vous.
              </p>
            </div>
          </div>

          <p class="danger-text">
            La suppression de votre compte entraînera la perte définitive de vos informations
            et de l’historique associé à cet accès utilisateur.
          </p>

          <div class="danger-note">
            ⚠️ Cette action ne supprime pas les commandes déjà traitées par les pharmacies.
            Elles restent conservées à des fins de traçabilité.
          </div>

          <form method="POST" action="{{ route('profile.destroy') }}" style="margin-top:14px;"
                onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ? Cette action est irréversible.');">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger">
              🗑️ Supprimer mon compte
            </button>
          </form>
        </div>
      </aside>

    </div>
  </main>
</div>

</body>
</html>
