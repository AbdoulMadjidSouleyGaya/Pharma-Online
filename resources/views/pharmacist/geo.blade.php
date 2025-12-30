<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Validation de présence - Espace Pharmacien</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --bg:#020617;
      --panel:#020617;
      --border:#1f2937;
      --ink:#e5e7eb;
      --muted:#9ca3af;
      --accent:#22c55e;
      --accent-strong:#16a34a;
      --danger:#f97373;
    }
    *{box-sizing:border-box}
    html,body{
      margin:0;
      min-height:100vh;
      background:
        radial-gradient(circle at top, rgba(34,197,94,.25), transparent 55%),
        radial-gradient(circle at bottom, rgba(59,130,246,.22), transparent 60%),
        #020617;
      color:var(--ink);
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
    }
    a{color:inherit;text-decoration:none}

    .shell{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:20px 16px;
    }

    .card{
      width:100%;
      max-width:540px;
      background:radial-gradient(circle at top left, rgba(148,163,184,.25), transparent 65%),
                 rgba(15,23,42,.96);
      border-radius:22px;
      border:1px solid rgba(15,23,42,.9);
      box-shadow:
        0 24px 80px rgba(15,23,42,.9),
        0 0 0 1px rgba(15,23,42,1);
      padding:20px 20px 18px;
      position:relative;
      overflow:hidden;
    }

    .card::before{
      content:"";
      position:absolute;
      inset:-40%;
      background:
        radial-gradient(circle at top, rgba(34,197,94,.25), transparent 60%),
        radial-gradient(circle at bottom right, rgba(59,130,246,.18), transparent 60%);
      opacity:.7;
      pointer-events:none;
    }

    .card-inner{
      position:relative;
      z-index:1;
    }

    .badge{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:4px 9px;
      border-radius:999px;
      font-size:11px;
      background:rgba(15,23,42,.9);
      border:1px solid rgba(148,163,184,.5);
      color:var(--muted);
      margin-bottom:8px;
    }
    .badge span.dot{
      width:7px;height:7px;border-radius:999px;
      background:#22c55e;
      box-shadow:0 0 10px rgba(34,197,94,.9);
    }

    h1{
      margin:2px 0 4px;
      font-size:22px;
      font-weight:800;
      letter-spacing:.03em;
    }
    .subtitle{
      margin:0;
      font-size:13px;
      color:var(--muted);
    }

    .pharma-line{
      margin-top:12px;
      font-size:13px;
      color:var(--muted);
    }
    .pharma-line strong{
      color:var(--ink);
    }

    .panel{
      margin-top:14px;
      padding:14px 14px 12px;
      border-radius:16px;
      background:rgba(15,23,42,.95);
      border:1px solid rgba(30,64,175,.65);
      font-size:13px;
    }
    .panel h2{
      margin:0 0 5px;
      font-size:13px;
      text-transform:uppercase;
      letter-spacing:.08em;
      color:#e5e7eb;
    }
    .panel p{
      margin:0;
      font-size:12px;
      color:var(--muted);
    }

    .geo-box{
      margin-top:14px;
      padding:12px 14px;
      border-radius:14px;
      background:rgba(15,23,42,.9);
      border:1px dashed rgba(148,163,184,.7);
      font-size:13px;
    }
    .geo-box-title{
      font-weight:600;
      margin-bottom:4px;
      display:flex;
      align-items:center;
      gap:6px;
    }
    .geo-box-title span.icon{font-size:16px;}
    .geo-box small{
      display:block;
      margin-top:2px;
      font-size:11px;
      color:var(--muted);
    }

    #btn-geo-check{
      margin-top:10px;
      border-radius:999px;
      border:1px solid var(--accent);
      background:linear-gradient(135deg,var(--accent),var(--accent-strong));
      color:#022c22;
      padding:8px 16px;
      font-size:13px;
      font-weight:700;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      gap:6px;
      box-shadow:0 12px 40px rgba(22,163,74,.75);
      transition:transform .16s ease, box-shadow .16s ease, filter .16s ease;
    }
    #btn-geo-check:hover{
      transform:translateY(-1px);
      filter:brightness(1.05);
      box-shadow:0 16px 50px rgba(22,163,74,.9);
    }

    #geo-status{
      display:block;
      margin-top:8px;
      font-size:12px;
      color:var(--muted);
      min-height:16px;
    }

    .footer-line{
      margin-top:14px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      font-size:11px;
      color:var(--muted);
    }
    .footer-line a.link{
      font-weight:600;
      text-decoration:underline;
      text-underline-offset:2px;
      color:#bfdbfe;
    }

    .alert{
      margin-top:10px;
      border-radius:12px;
      padding:8px 10px;
      font-size:12px;
      display:flex;
      gap:8px;
      align-items:flex-start;
    }
    .alert.warn{
      background:rgba(30,64,175,.9);
      border:1px solid rgba(191,219,254,.8);
      color:#e0f2fe;
    }
    .alert.error{
      background:rgba(69,10,10,.9);
      border:1px solid rgba(248,113,113,.75);
      color:#fecaca;
    }
  </style>
</head>
<body>
<div class="shell">
  <div class="card">
    <div class="card-inner">

      <div class="badge">
        <span class="dot"></span>
        Double sécurité activée · Contrôle de présence
      </div>

      <h1>Validation de présence en pharmacie</h1>
      <p class="subtitle">
        Dernière étape avant d’accéder à votre espace de gestion.
      </p>

      <div class="pharma-line">
        Connecté en tant que <strong>{{ $user->name }}</strong>
        @if($pharmacy)
          <br>Pharmacie : <strong>{{ $pharmacy->name }}</strong>
        @else
          <br><strong>Aucune pharmacie liée à ce compte.</strong>
        @endif
      </div>

      @if($hint)
        <div class="alert warn">
          <span>⚠️</span>
          <div>{{ $hint }}</div>
        </div>
      @endif

      <div class="panel">
        <h2>Pourquoi cette vérification&nbsp;?</h2>
        <p>
          Pour éviter qu’un compte pharmacien traite des commandes en dehors de la pharmacie,
          PharmaOnline vérifie que vous vous trouvez dans un rayon d’environ
          <strong> 500 m autour de votre officine</strong>
          avant d’ouvrir le tableau de bord.
        </p>

      </div>

      <div class="geo-box">
        <div class="geo-box-title">
          <span class="icon">📍</span>
          <span>Valider ma position actuelle</span>
        </div>
        <small>
          Votre navigateur vous demandera l’autorisation d’accéder à votre position.
          Assurez-vous d’être physiquement présent dans un rayon d’environ
          <strong>500 m</strong> de la pharmacie pour que la vérification soit acceptée.
        </small>

        <button type="button" id="btn-geo-check">
          📡 Lancer la vérification de présence
        </button>

        <span id="geo-status"></span>
      </div>

      <div class="footer-line">
        <span>
          En cas de problème de localisation, vérifiez le GPS de votre appareil
          (Wi-Fi / données activés) ou contactez l’administrateur.
        </span>
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form-geo').submit();"
           class="link">
          Se déconnecter
        </a>
      </div>

      <form id="logout-form-geo" method="POST" action="{{ route('logout') }}" style="display:none;">
        @csrf
      </form>

    </div>
  </div>
</div>

<script>
  (function () {
    const btn   = document.getElementById('btn-geo-check');
    const label = document.getElementById('geo-status');
    if (!btn || !label) return;

    const url   = "{{ route('pharmacist.geo.check') }}";
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    btn.addEventListener('click', function () {
      label.textContent = "Récupération de votre position…";
      label.style.color = "#9ca3af";

      if (!navigator.geolocation) {
        label.textContent = "Votre navigateur ne supporte pas la géolocalisation.";
        label.style.color = "#fecaca";
        return;
      }

      navigator.geolocation.getCurrentPosition(
        function (pos) {
          const payload = {
            latitude:  pos.coords.latitude,
            longitude: pos.coords.longitude
          };

          label.textContent = "Vérification de votre position par le serveur…";

          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': token
            },
            body: JSON.stringify(payload)
          })
          .then(r => r.json().then(data => ({ok: r.ok, status: r.status, body: data})))
          .then(res => {
            if (res.ok && res.body.status === 'ok') {
              label.textContent = res.body.message;
              label.style.color = "#bbf7d0";
              // redirection vers le dashboard si OK
              if (res.body.redirect) {
                setTimeout(() => {
                  window.location.href = res.body.redirect;
                }, 800);
              }
            } else {
              label.textContent = (res.body && res.body.message)
                ? res.body.message
                : "Impossible de valider la position.";
              label.style.color = "#fecaca";
            }
          })
          .catch(() => {
            label.textContent = "Erreur réseau lors de la vérification.";
            label.style.color = "#fecaca";
          });
        },
        function (err) {
          if (err.code === 1) {
            label.textContent =
              "Permission de géolocalisation refusée par le navigateur. " +
              "Vérifiez les autorisations de localisation pour ce site ou utilisez un navigateur comme Chrome/Edge.";
          } else {
            label.textContent = "Impossible de récupérer votre position (code " + err.code + ").";
          }
          label.style.color = "#fecaca";
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
        }
      );
    });
  })();
</script>
</body>
</html>
