<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Tableau de bord Pharmacien</title>
  <style>
    :root{--blue:#2563eb;--bg:#f3f6fb;--ink:#111827}
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--ink);font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
    .wrap{max-width:1100px;margin:28px auto;padding:0 16px}
    .top{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
    h1{margin:0;font-size:28px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px}
    .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:16px;margin-top:16px}
    .col-4{grid-column:span 4}
    .col-8{grid-column:span 8}
    .muted{color:#6b7280}
    .btn{appearance:none;border:1px solid #cbd5e1;background:#fff;border-radius:10px;padding:10px 14px;font-weight:600;text-decoration:none;color:#111827;cursor:pointer}
    .btn:hover{background:#f1f5f9}
    .btn.primary{background:var(--blue);color:#fff;border-color:#1d4ed8}
    .kpi{display:flex;gap:14px;align-items:center}
    .kpi .num{font-size:26px;font-weight:800}
    .list{margin:0;padding:0;list-style:none}
    .list li{padding:10px 0;border-bottom:1px solid #eef2f7}
    .list li:last-child{border-bottom:none}
    .row{display:flex;justify-content:space-between;gap:10px;align-items:center}
    .pill{display:inline-block;padding:4px 10px;border-radius:999px;background:#eef2ff;color:#1e40af;font-weight:700;font-size:12px}
    .flex{display:flex;gap:8px;align-items:center}
  </style>
</head>
<body>
  <div class="wrap">

    <div class="top">
      <h1>Bienvenue, {{ $user->name }}</h1>
      <form method="POST" action="{{ route('logout') }}" class="flex">
        @csrf
        <a class="btn" href="{{ route('profile.edit') }}">Mon profil</a>
        <button class="btn" type="submit">Se déconnecter</button>
      </form>
    </div>

    <div class="grid">
      <div class="col-8">
        <div class="card">
          <div class="row">
            <h2 style="margin:0">Ma Pharmacie</h2>
            @if($pharmacy)
              <span class="pill">ID #{{ $pharmacy->id }}</span>
            @endif
          </div>
          @if($pharmacy)
            <ul class="list" style="margin-top:10px">
              <li class="row">
                <span>Nom</span>
                <strong>{{ $pharmacy->name }}</strong>
              </li>
              <li class="row">
                <span>Email</span>
                <strong>{{ $pharmacy->email ?? '—' }}</strong>
              </li>
              <li class="row">
                <span>Quartier</span>
                <strong>{{ $pharmacy->district ?? '—' }}</strong>
              </li>
              <li class="row">
                <span>Localisation</span>
                <strong>{{ $pharmacy->address ?? '—' }}</strong>
              </li>
              <li class="row">
                <span>Contact</span>
                <strong>{{ $pharmacy->phone ?? '—' }}</strong>
              </li>
            </ul>
          @else
            <p class="muted" style="margin-top:8px">Aucune pharmacie rattachée à ce compte.</p>
          @endif
        </div>
      </div>

      <div class="col-4">
        <div class="card">
          <h2 style="margin:0 0 8px">Actions rapides</h2>
          <div class="flex" style="flex-direction:column;align-items:stretch">
            <a class="btn" href="{{ route('products.search') }}">🔎 Recherche de produits</a>
            <a class="btn" href="{{ route('contact') }}">📩 Contacter le support</a>
            <a class="btn" href="{{ url('/') }}">🏠 Accueil</a>
          </div>
          <hr style="margin:14px 0;border:none;border-top:1px solid #eef2f7">
          <div class="kpi">
            <div>
              <div class="muted">Statut mot de passe</div>
              <div class="num">
                @if($user->password_is_temp)
                  Temporaire
                @else
                  OK
                @endif
              </div>
            </div>
          </div>
          @if($user->password_is_temp && $user->temp_password_expires_at)
            <p class="muted" style="margin-top:8px">
              Expire le {{ \Carbon\Carbon::parse($user->temp_password_expires_at)->format('d/m/Y H:i') }}.
              <br>Changez-le depuis <a href="{{ route('profile.edit') }}">Mon profil</a>.
            </p>
          @endif
        </div>
      </div>
    </div>

  </div>
</body>
</html>
