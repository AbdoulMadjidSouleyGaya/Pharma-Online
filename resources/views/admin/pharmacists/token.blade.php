<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Token API AbdouPharma</title>

  <style>
    :root{
      --bg:#0f172a;
      --surface:#0b1120;
      --card:#020617;
      --border:#1e293b;
      --accent:#22c55e;
      --accent-soft:#bbf7d0;
      --accent-strong:#16a34a;
      --danger:#ef4444;
      --ink:#e5e7eb;
      --muted:#9ca3af;
      --chip:#111827;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:radial-gradient(circle at top left, #22c55e 0, #0f172a 40%, #020617 100%);
      color:var(--ink);
      min-height:100vh;
    }
    .page{
      max-width:960px;
      margin:28px auto 40px;
      padding:0 18px;
    }

    .header-shell{
      background:radial-gradient(circle at top left, rgba(34,197,94,.35), transparent 60%),
                  radial-gradient(circle at bottom right, rgba(56,189,248,.25), transparent 55%),
                  linear-gradient(135deg, #020617, #020617);
      border-radius:22px;
      border:1px solid rgba(148,163,184,.35);
      padding:18px 18px 18px;
      box-shadow:
        0 18px 60px rgba(15,23,42,.8),
        0 0 0 1px rgba(15,23,42,.9);
      position:relative;
      overflow:hidden;
    }
    .header-shell::before{
      content:"";
      position:absolute;
      inset:-40%;
      background:
        radial-gradient(circle at top, rgba(34,197,94,.16), transparent 65%),
        radial-gradient(circle at right, rgba(59,130,246,.10), transparent 70%);
      opacity:1;
      pointer-events:none;
    }
    .header-inner{
      position:relative;
      display:flex;
      flex-wrap:wrap;
      justify-content:space-between;
      gap:16px;
      align-items:flex-start;
    }
    h1{
      margin:0 0 4px;
      font-size:24px;
      font-weight:800;
      letter-spacing:.02em;
      color:#f9fafb;
    }
    .headline-sub{
      margin:0;
      font-size:13px;
      color:var(--muted);
    }
    .pill-row{
      display:flex;
      flex-wrap:wrap;
      gap:6px;
      margin-top:10px;
    }
    .pill{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:4px 9px;
      border-radius:999px;
      font-size:11px;
      background:rgba(15,23,42,.8);
      color:var(--muted);
      border:1px solid rgba(148,163,184,.45);
    }
    .pill strong{
      color:#e5e7eb;
      font-weight:600;
    }
    .status-pill{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:4px 10px;
      border-radius:999px;
      font-size:11px;
      font-weight:600;
      border:1px solid rgba(34,197,94,.25);
      background:rgba(22,163,74,.13);
      color:#bbf7d0;
    }
    .status-pill.red{
      border-color:rgba(239,68,68,.35);
      background:rgba(127,29,29,.55);
      color:#fecaca;
    }
    .status-dot{
      width:7px;
      height:7px;
      border-radius:999px;
      background:#22c55e;
      box-shadow:0 0 12px rgba(34,197,94,.9);
    }
    .status-dot.red{
      background:#fb7185;
      box-shadow:0 0 10px rgba(248,113,113,.8);
    }

    .header-actions{
      display:flex;
      flex-direction:column;
      align-items:flex-end;
      gap:10px;
    }
    .btn{
      appearance:none;
      border-radius:999px;
      padding:7px 14px;
      font-size:13px;
      font-weight:600;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      gap:7px;
      border:1px solid rgba(148,163,184,.6);
      background:rgba(15,23,42,.9);
      color:#e5e7eb;
      text-decoration:none;
      transition:background .18s ease, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .btn:hover{
      background:rgba(15,23,42,1);
      transform:translateY(-1px);
      box-shadow:0 15px 35px rgba(15,23,42,.7);
    }
    .btn span.icon{
      font-size:15px;
    }
    .tag-small{
      font-size:11px;
      color:var(--muted);
    }

    .body-shell{
      margin-top:18px;
      display:grid;
      grid-template-columns: minmax(0, 1.6fr) minmax(0, 1.4fr);
      gap:18px;
    }
    @media (max-width: 820px){
      .body-shell{grid-template-columns:1fr;}
    }

    .panel{
      background:rgba(15,23,42,.96);
      border-radius:20px;
      border:1px solid rgba(30,64,175,.55);
      padding:16px 16px 18px;
      box-shadow:
        0 12px 35px rgba(15,23,42,.85),
        0 0 0 1px rgba(15,23,42,.9);
    }
    .panel.soft{
      border-color:rgba(148,163,184,.35);
    }

    .panel h2{
      margin:0 0 4px;
      font-size:14px;
      font-weight:700;
      letter-spacing:.03em;
      text-transform:uppercase;
      color:#e5e7eb;
    }
    .panel p.desc{
      margin:0;
      font-size:12px;
      color:var(--muted);
    }

    dl.meta{
      margin:12px 0 0;
      padding:0;
      list-style:none;
      font-size:13px;
    }
    dl.meta div{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:6px;
    }
    dl.meta dt{
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:.06em;
      color:var(--muted);
    }
    dl.meta dd{
      margin:0;
      font-weight:500;
      color:#e5e7eb;
      text-align:right;
    }
    .chip{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:3px 9px;
      border-radius:999px;
      font-size:11px;
      background:#020617;
      color:var(--muted);
      border:1px solid rgba(51,65,85,.9);
    }
    .chip.green{
      color:#bbf7d0;
      border-color:rgba(74,222,128,.45);
      background:rgba(5,46,22,.9);
    }
    .chip.red{
      color:#fecaca;
      border-color:rgba(248,113,113,.5);
      background:rgba(69,10,10,.95);
    }

    .token-preview{
      margin-top:8px;
      padding:8px 10px;
      border-radius:10px;
      background:rgba(15,23,42,.92);
      border:1px dashed rgba(148,163,184,.4);
      font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-size:11px;
      color:var(--muted);
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
    }

    .field{margin-top:12px;}
    label{
      display:block;
      font-size:12px;
      font-weight:600;
      color:#e5e7eb;
      margin-bottom:4px;
    }
    .input{
      width:100%;
      padding:9px 11px;
      border-radius:10px;
      border:1px solid rgba(148,163,184,.55);
      background:rgba(15,23,42,.9);
      color:#e5e7eb;
      font-size:13px;
      font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      outline:none;
      transition:border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }
    .input::placeholder{
      color:#6b7280;
    }
    .input:focus{
      border-color:rgba(34,197,94,.65);
      box-shadow:0 0 0 1px rgba(34,197,94,.45);
      background:rgba(15,23,42,.98);
    }

    .help{
      font-size:11px;
      color:var(--muted);
      margin-top:4px;
      line-height:1.5;
    }
    .error{
      font-size:11px;
      color:#fecaca;
      margin-top:4px;
    }

    .form-footer{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      margin-top:16px;
      font-size:11px;
      color:var(--muted);
    }
    .btn-primary{
      border-radius:999px;
      padding:8px 15px;
      font-size:13px;
      font-weight:700;
      border:none;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      gap:7px;
      background:linear-gradient(135deg, var(--accent), var(--accent-strong));
      color:#022c22;
      box-shadow:
        0 0 0 1px rgba(22,163,74,.4),
        0 10px 30px rgba(22,163,74,.55);
      transition:transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }
    .btn-primary:hover{
      transform:translateY(-1px);
      filter:brightness(1.05);
      box-shadow:
        0 0 0 1px rgba(22,163,74,.55),
        0 16px 40px rgba(22,163,74,.8);
    }
    .btn-primary span.icon{
      font-size:15px;
    }

    .alert{
      margin-top:12px;
      border-radius:10px;
      padding:8px 10px;
      font-size:12px;
      display:flex;
      gap:8px;
      align-items:flex-start;
    }
    .alert.ok{
      background:rgba(5,46,22,.9);
      border:1px solid rgba(34,197,94,.6);
      color:#bbf7d0;
    }
    .alert.hint{
      background:rgba(30,64,175,.9);
      border:1px solid rgba(191,219,254,.7);
      color:#e0f2fe;
    }

  </style>
</head>
<body>
  <div class="page">

    <div class="header-shell">
      <div class="header-inner">
        <div>
          <h1>Token API AbdouPharma</h1>
          <p class="headline-sub">
            Configuration centrale du token + position GPS de la pharmacie utilisée par l’espace Pharmacien.
          </p>

          <div class="pill-row">
            <div class="pill">
              👤 <strong>{{ $pharmacist->name }}</strong>
              <span style="opacity:.7;">&lt;{{ $pharmacist->email }}&gt;</span>
            </div>

            @if($pharmacy)
              <div class="pill">
                🏥 <strong>{{ $pharmacy->name }}</strong>
              </div>
            @endif
          </div>
        </div>

        <div class="header-actions">
          <a href="{{ route('admin.pharmacists.index') }}" class="btn">
            <span class="icon">←</span>
            <span>Retour à la liste</span>
          </a>

          @if($pharmacy && $pharmacy->api_token)
            <div class="status-pill">
              <span class="status-dot"></span>
              <span>Token configuré pour cette pharmacie</span>
            </div>
          @else
            <div class="status-pill red">
              <span class="status-dot red"></span>
              <span>Token manquant – à renseigner</span>
            </div>
          @endif

          <div class="tag-small">
            La géolocalisation (latitude / longitude) est utilisée pour restreindre la connexion
            à un rayon défini autour de la pharmacie.
          </div>
        </div>
      </div>
    </div>

    <div class="body-shell">

      {{-- Colonne gauche : infos --}}
      <section class="panel">
        <h2>Contexte & état</h2>
        <p class="desc">
          Le token et la position GPS sont stockés au niveau de la pharmacie.
          L’espace Pharmacien les utilise pour se connecter à AbdouPharma et vérifier la présence
          physique du pharmacien dans la pharmacie.
        </p>

        <dl class="meta">
          <div>
            <dt>Pharmacie</dt>
            <dd>
              @if($pharmacy)
                <span class="chip">{{ $pharmacy->name }}</span>
              @else
                <span class="chip red">Aucune pharmacie liée</span>
              @endif
            </dd>
          </div>

          <div>
            <dt>État actuel du token</dt>
            <dd>
              @if($pharmacy && $pharmacy->api_token)
                <span class="chip green">Configuré</span>
              @else
                <span class="chip red">Non configuré</span>
              @endif
            </dd>
          </div>

          <div>
            <dt>Position GPS</dt>
            <dd>
              @if($pharmacy && $pharmacy->latitude && $pharmacy->longitude)
                <span class="chip">
                  📍 {{ $pharmacy->latitude }}, {{ $pharmacy->longitude }}
                </span>
              @else
                <span class="chip red">Non définie</span>
              @endif
            </dd>
          </div>
        </dl>

        @if($pharmacy && $pharmacy->api_token)
          <div class="token-preview" title="Token stocké côté base (affiché partiellement)">
            Token actuel : ************{{ substr($pharmacy->api_token, -6) }}
          </div>
        @else
          <div class="token-preview">
            Aucun token enregistré pour l’instant. Une fois défini, il sera utilisé 
            automatiquement par tous les appels API de cette pharmacie.
          </div>
        @endif

        <p class="help" style="margin-top:10px;">
          <strong>Rappel :</strong> le token est généré dans l’instance AbdouPharma (Sanctum / API).
          <br>Les coordonnées GPS peuvent être récupérées via Google Maps, un smartphone ou un GPS.
        </p>
      </section>

      {{-- Colonne droite : formulaire --}}
      <section class="panel soft">
        <h2>Mettre à jour le token & la position</h2>
        <p class="desc">
          Colle le token AbdouPharma et, si possible, renseigne la latitude / longitude
          de la pharmacie pour activer le contrôle de présence du pharmacien.
        </p>

        @if($errors->any())
          <div class="alert hint">
            <span>⚠️</span>
            <div>
              @foreach($errors->all() as $e)
                <div>{{ $e }}</div>
              @endforeach
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.pharmacists.token.update', $pharmacist) }}">
          @csrf

          {{-- Token --}}
          <div class="field" style="margin-top:14px;">
            <label for="api_token">Token API AbdouPharma</label>
            <input id="api_token"
                   name="api_token"
                   type="text"
                   class="input"
                   autocomplete="off"
                   spellcheck="false"
                   value="{{ old('api_token', $pharmacy->api_token ?? '') }}"
                   placeholder="Ex : 1|Xsdf... (token Sanctum généré dans AbdouPharma)">
            @error('api_token')
              <div class="error">{{ $message }}</div>
            @enderror
            <div class="help">
              Colle le token complet tel qu’il est fourni par AbdouPharma.
              Il sera stocké côté serveur et utilisé par la passerelle API.
            </div>
          </div>

          {{-- Latitude --}}
          <div class="field" style="margin-top:14px;">
            <label for="latitude">Latitude (°)</label>
            <input id="latitude"
                   name="latitude"
                   type="number"
                   step="0.0000001"
                   class="input"
                   value="{{ old('latitude', $pharmacy->latitude ?? '') }}"
                   placeholder="Ex : 13.5123456">
            @error('latitude')
              <div class="error">{{ $message }}</div>
            @enderror>
            <div class="help">
              Valeur entre -90 et 90.  
              Exemple pour Niamey : autour de <code>13.xxxxxx</code>.
            </div>
          </div>

          {{-- Longitude --}}
          <div class="field" style="margin-top:10px;">
            <label for="longitude">Longitude (°)</label>
            <input id="longitude"
                   name="longitude"
                   type="number"
                   step="0.0000001"
                   class="input"
                   value="{{ old('longitude', $pharmacy->longitude ?? '') }}"
                   placeholder="Ex : 2.1234567">
            @error('longitude')
              <div class="error">{{ $message }}</div>
            @enderror
            <div class="help">
              Valeur entre -180 et 180.  
              Ces coordonnées serviront pour vérifier que le pharmacien est bien
              dans un rayon défini autour de la pharmacie avant de travailler.
            </div>
          </div>

          <div class="form-footer">
            <div>
              Le contrôle de présence utilisera ces coordonnées avec un rayon de sécurité
              (par exemple ~50m) pour tenir compte de la précision GPS.
            </div>

            <button type="submit" class="btn-primary">
              <span class="icon">💾</span>
              <span>Enregistrer</span>
            </button>
          </div>
        </form>
      </section>
    </div>
  </div>
</body>
</html>
