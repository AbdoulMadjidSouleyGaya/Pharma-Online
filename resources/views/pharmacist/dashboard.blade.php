<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Tableau de bord Pharmacien</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    :root{
      --blue:#2563eb; --blue2:#1d4ed8; --blue3:#60a5fa;
      --ok:#10b981; --warn:#f59e0b; --danger:#ef4444;
      --bg:#0b1220; --panel:rgba(255,255,255,.06); --ink:#e5e7eb; --muted:#9aa4b2; --border:rgba(255,255,255,.12);
      --shadow:0 10px 30px rgba(0,0,0,.25);
      --radius:16px;
    }
    *{box-sizing:border-box}
    html,body{
      margin:0;
      background:linear-gradient(160deg,#0b1020 0%, #0b1220 55%, #0a1528 100%);
      color:var(--ink);
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif
    }
    a{color:inherit;text-decoration:none}
    .shell{min-height:100dvh;display:flex;flex-direction:column}

    /* Topbar */
    .topbar{
      position:sticky;top:0;z-index:10;
      background:linear-gradient(100deg,#1e3a8a 0%, var(--blue) 50%, var(--blue3) 100%);
      color:#fff;
      border-bottom:1px solid #ffffff22;
      box-shadow:0 4px 18px rgba(0,0,0,.18)
    }
    .topbar-in{
      max-width:1200px;
      margin:0 auto;
      padding:12px 16px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px
    }
    .brand{
      display:flex;
      align-items:center;
      gap:10px;
      font-weight:800;
      letter-spacing:.3px
    }
    .brand .logo{
      width:32px;height:32px;border-radius:10px;
      background:#ffffff1a;border:1px solid #ffffff40;
      display:grid;place-items:center;
      font-size:14px;
      backdrop-filter:blur(8px)
    }
    .top-actions{
      display:flex;
      align-items:center;
      gap:8px;
      flex-wrap:wrap
    }

    .btn{
      appearance:none;
      border:1px solid #2b3b63;
      background:#0e1737;
      border-radius:12px;
      padding:10px 14px;
      font-weight:700;
      text-decoration:none;
      color:#e5e7eb;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      gap:8px;
      justify-content:center;
      transition:transform .06s ease, box-shadow .12s ease, background .12s ease, opacity .12s ease
    }
    .btn:hover{
      transform:translateY(-1px);
      box-shadow:0 8px 20px rgba(0,0,0,.22)
    }
    .btn.outline{
      background:transparent;
      color:#fff;
      border-color:#ffffff88
    }
    .btn.outline:hover{background:#ffffff22}
    .btn.warn{background:var(--warn);border-color:#d97706;color:#111}
    .btn.ok{background:var(--ok);border-color:#059669;color:#fff}
    .btn.danger{background:var(--danger);border-color:#dc2626;color:#fff}
    .btn.primary{background:var(--blue);border-color:var(--blue2);color:#fff}

    .btn.is-busy{
      opacity:.6;
      pointer-events:none;
    }
    .btn .spinner{
      margin-left:6px;
      width:12px;height:12px;
      border-radius:999px;
      border:2px solid #9ca3af;
      border-top-color:transparent;
      animation:spin .7s linear infinite;
    }
    @keyframes spin{
      to{transform:rotate(360deg);}
    }

    .wrap{
      max-width:1200px;
      margin:28px auto;
      padding:0 16px
    }

    /* Cards / sections */
    .glass{
      background:var(--panel);
      border:1px solid var(--border);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      backdrop-filter:blur(10px);
    }

    .hero{padding:22px 20px}
    .hero h1{margin:0 0 6px;font-size:28px}
    .hero p{margin:0;color:var(--muted)}
    .hero-sub{
      margin-top:10px;
      font-size:12px;
      color:var(--muted);
      display:flex;
      align-items:center;
      gap:8px;
      flex-wrap:wrap;
    }
    .hero-chip{
      padding:3px 9px;
      border-radius:999px;
      font-size:11px;
      border:1px solid rgba(148,163,184,.4);
      background:rgba(15,23,42,.6);
      color:#9ca3af;
      display:inline-flex;
      align-items:center;
      gap:6px;
    }
    .hero-chip span.dot{
      width:7px;height:7px;border-radius:999px;
      background:#22c55e;
      box-shadow:0 0 8px rgba(34,197,94,.8);
    }

    /* KPI */
    .kpi-grid{
      margin-top:16px;
      display:grid;
      grid-template-columns:repeat(12,1fr);
      gap:14px
    }
    .kpi{
      grid-column:span 4;
      padding:18px;
      display:flex;
      align-items:center;
      gap:14px;
      position:relative;
      overflow:hidden
    }
    .kpi::after{
      content:"";
      position:absolute;
      inset:0;
      border-radius:inherit;
      background:radial-gradient(120px 120px at 20% -20%, rgba(255,255,255,.12), transparent 60%);
      pointer-events:none
    }
    .kpi .icon{
      width:46px;height:46px;border-radius:12px;
      display:grid;place-items:center;
      color:#fff;font-weight:800;
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.25)
    }
    .kpi .meta{line-height:1.15}
    .kpi .meta .label{font-size:13px;color:var(--muted)}
    .kpi .meta .num{font-size:24px;font-weight:900;margin-top:4px}

    .i-warn{background:linear-gradient(140deg,#f59e0b,#fbbf24)}
    .i-ok{background:linear-gradient(140deg,#10b981,#34d399)}
    .i-danger{background:linear-gradient(140deg,#ef4444,#f87171)}

    /* Actions */
    .section{margin-top:18px}
    .section h2{margin:0 0 10px;font-size:18px}
    .actions-grid{
      display:grid;
      grid-template-columns:repeat(12,1fr);
      gap:14px
    }
    .action{
      grid-column:span 6;
      padding:16px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      transition:transform .08s ease, border-color .12s ease, background .12s ease
    }
    .action:hover{transform:translateY(-2px)}
    .action .left{
      display:flex;
      align-items:center;
      gap:12px
    }
    .action .badge{
      font-weight:900;
      font-size:12px;
      color:#111;
      border-radius:999px;
      padding:6px 10px;
      letter-spacing:.3px
    }
    .badge-warn{background:#f59e0b}
    .badge-ok{background:#10b981;color:#fff}
    .badge-danger{background:#ef4444;color:#fff}
    .badge-primary{background:#2563eb;color:#fff}
    .action .title{font-weight:900}
    .action .hint{font-size:12px;color:var(--muted)}
    .go{white-space:nowrap}

    .alert{
      margin-top:16px;
      background:#fff7ed1a;
      border:1px solid #fed7aa40;
      color:#fde68a;
      padding:12px 14px;
      border-radius:12px
    }

    /* Toast nouvelle commande */
    #new-order-toast{
      position:fixed;
      right:16px;
      bottom:16px;
      z-index:40;
      max-width:340px;
      display:flex;
      align-items:flex-start;
      gap:8px;
      border-radius:16px;
      border:1px solid #6ee7b7;
      background:#ecfdf5;
      color:#065f46;
      padding:10px 14px;
      box-shadow:0 14px 35px rgba(0,0,0,.4);
      opacity:0;
      transform:translateY(12px);
      pointer-events:none;
      transition:opacity .25s ease-out, transform .25s ease-out;
      font-size:13px;
    }
    #new-order-toast.show{
      opacity:1;
      transform:translateY(0);
      pointer-events:auto;
    }

    @media(max-width:1000px){
      .kpi{grid-column:span 6}
      .action{grid-column:span 12}
    }
    @media(max-width:720px){
      .kpi{grid-column:span 12}
    }
  </style>
</head>
<body>
<div class="shell">

  <!-- Topbar -->
  <header class="topbar">
    <div class="topbar-in">
      <div class="brand">
        <div class="logo">Rx</div>
        <div>Espace Pharmacien</div>
      </div>
      <div class="top-actions">
        <span style="color:#f0f4ff;font-weight:700">
          Bonjour, {{ $user->name }}
        </span>
        <a class="btn outline" href="{{ route('pharmacist.manage') }}">⚙ Gestion</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn outline" type="submit">Se déconnecter</button>
        </form>
      </div>
    </div>
  </header>

  <main class="wrap">

    <!-- Hero -->
    <section class="hero glass">
      <h1>Tableau de bord</h1>
      <p>Gérez vos commandes et suivez vos indicateurs en un coup d’œil.</p>

      <div class="hero-sub">
        <div class="hero-chip" id="presence-indicator">
          <span class="dot"></span>
          <span>Présence en pharmacie : vérification périodique active</span>
        </div>
        <span style="font-size:11px;color:#9ca3af;" id="presence-last-check">
          Dernier contrôle : en attente…
        </span>
      </div>
    </section>

    <!-- (optionnel) alerte mot de passe temporaire -->
    @if(!empty($user->password_is_temp) && !empty($user->temp_password_expires_at))
      <div class="alert glass" style="border-radius:12px">
        Votre mot de passe temporaire expire le
        <strong>{{ \Carbon\Carbon::parse($user->temp_password_expires_at)->format('d/m/Y H:i') }}</strong>.
        Pensez à le changer depuis votre profil.
      </div>
    @endif

    <!-- KPIs -->
    <section class="kpi-grid">
      <div class="kpi glass">
        <div class="icon i-warn">⏳</div>
        <div class="meta">
          <div class="label">Commandes en attente</div>
          <div class="num" data-pending-count>{{ $pendingCount ?? 0 }}</div>
        </div>
      </div>
      <div class="kpi glass">
        <div class="icon i-ok">✅</div>
        <div class="meta">
          <div class="label">Validées</div>
          <div class="num">{{ $validatedCount ?? 0 }}</div>
        </div>
      </div>
      <div class="kpi glass">
        <div class="icon i-danger">⛔</div>
        <div class="meta">
          <div class="label">Rejetées</div>
          <div class="num">{{ $rejectedCount ?? 0 }}</div>
        </div>
      </div>
    </section>

    <!-- Actions rapides -->
    <section class="section">
      <h2>Actions rapides</h2>
      <div class="actions-grid">

        <div class="action glass">
          <div class="left">
            <span class="badge badge-warn">ATTENTE</span>
            <div>
              <div class="title">Commandes en attente</div>
              <div class="hint">À traiter dès que possible</div>
            </div>
          </div>
          <a class="btn warn go"
             data-orders-link
             href="{{ route('pharmacist.orders.index', ['status' => 'pending']) }}">
            Ouvrir
          </a>
        </div>

        <div class="action glass">
          <div class="left">
            <span class="badge badge-ok">VALIDÉE</span>
            <div>
              <div class="title">Commandes validées</div>
              <div class="hint">Historique des validations</div>
            </div>
          </div>
          <a class="btn ok go"
             data-orders-link
             href="{{ route('pharmacist.orders.index', ['status' => 'validated']) }}">
            Ouvrir
          </a>
        </div>

        <div class="action glass">
          <div class="left">
            <span class="badge badge-danger">REJETÉE</span>
            <div>
              <div class="title">Commandes rejetées</div>
              <div class="hint">Avec motif et suivi</div>
            </div>
          </div>
          <a class="btn danger go"
             data-orders-link
             href="{{ route('pharmacist.orders.index', ['status' => 'rejected']) }}">
            Ouvrir
          </a>
        </div>

        <div class="action glass">
          <div class="left">
            <span class="badge badge-primary">RAPPORT</span>
            <div>
              <div class="title">Rapport & Statistiques</div>
              <div class="hint">Tendances et synthèse</div>
            </div>
          </div>
          <a class="btn primary go" href="{{ route('pharmacist.reports') }}">
            Consulter
          </a>
        </div>

      </div>
    </section>

  </main>
</div>

<!-- 🔔 Son nouvelle commande -->
<audio id="newOrderSound" preload="auto" loop>
  <source src="/sounds/new-order.mp3" type="audio/mpeg">
</audio>

{{-- Toast temps réel pour les nouvelles commandes --}}
<div id="new-order-toast">
    <span style="margin-top:2px;font-size:16px;">🛎</span>
    <div class="flex-1">
        <div style="font-weight:600;font-size:13px;">Nouvelle commande reçue</div>
        <div style="font-size:12px;" data-order-number>
            Une nouvelle commande vient d’arriver pour cette pharmacie.
        </div>
    </div>
</div>

<script>
  // ----- Gestion son / alarme (sans besoin de clic dans le code) -----
  let alarmActive = false;
  const audio     = document.getElementById('newOrderSound');

  window.startOrderAlarm = function () {
    if (!audio) return;
    if (alarmActive) return;

    alarmActive       = true;
    audio.loop        = true;
    audio.currentTime = 0;

    console.log('🔊 Démarrage de l’alarme (autoplay)…');

    audio.play().catch((err) => {
      console.warn('⚠️ Le navigateur a peut-être bloqué la lecture automatique du son :', err);
    });
  };

  window.stopOrderAlarm = function () {
    if (!audio) return;
    if (!alarmActive) return;

    console.log('🛑 Arrêt de l’alarme');
    alarmActive       = false;
    audio.pause();
    audio.currentTime = 0;
  };

  // 🧪 utilitaire de test manuel (depuis la console navigateur)
  window.__testBeep = function () {
    if (!audio) return;
    alarmActive       = true;
    audio.loop        = false;
    audio.currentTime = 0;
    audio.play().catch(console.error);
  };
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.Echo === 'undefined') {
            console.warn('⚠️ window.Echo est indisponible. Vérifie l’inclusion de resources/js/app.js et ta config Echo.');
        }

        const PHARM_ID = {{ isset($pharmacy) && $pharmacy?->id ? (int) $pharmacy->id : 'null' }};

        if (PHARM_ID === null) {
            console.warn('⚠️ Aucune pharmacie associée à ce compte pharmacien. Écoute des commandes désactivée.');
        }

        const PENDING_AT_LOAD   = {{ (int)($pendingCount ?? 0) }};
        const pendingBadge      = document.querySelector('[data-pending-count]');
        const PENDING_COUNT_URL = "{{ route('pharmacist.orders.pendingCount') }}";

        window.currentPending = PENDING_AT_LOAD;
        console.log('📊 PENDING_AT_LOAD =', window.currentPending);

        function syncAlarmWithPending() {
            if (typeof window.currentPending === 'undefined') return;

            console.log('🔁 syncAlarmWithPending →', window.currentPending);

            if (window.currentPending > 0) {
                if (typeof window.startOrderAlarm === 'function') {
                    window.startOrderAlarm();
                }
            } else {
                if (typeof window.stopOrderAlarm === 'function') {
                    window.stopOrderAlarm();
                }
            }
        }

        function refreshPendingFromServer() {
            console.log('🌐 Rafraîchissement du nombre de commandes en attente…');

            fetch(PENDING_COUNT_URL, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
                .then(response => {
                    if (!response.ok) throw new Error('Erreur HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    const value = typeof data.pending === 'number' ? data.pending : 0;
                    window.currentPending = value;

                    console.log('✅ pendingCount depuis serveur =', value);

                    if (pendingBadge) {
                        pendingBadge.textContent = window.currentPending;
                    }

                    syncAlarmWithPending();
                })
                .catch(err => {
                    console.warn('⚠️ Impossible de rafraîchir le nombre de commandes en attente :', err);
                });
        }

        // sync avec la valeur initiale et tentative de démarrage de l’alarme
        syncAlarmWithPending();
        // 🔁 premier refresh serveur
        refreshPendingFromServer();
        // puis toutes les 10 secondes
        setInterval(refreshPendingFromServer, 10_000);

        // Toast
        const toast = document.getElementById('new-order-toast');
        const toastText = toast ? toast.querySelector('[data-order-number]') : null;

        function showNewOrderToast(orderNumber) {
            if (!toast) return;

            if (toastText && orderNumber) {
                toastText.textContent = `Commande #${orderNumber} reçue pour votre pharmacie.`;
            }

            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        if (typeof window.Echo !== 'undefined' && PHARM_ID !== null) {
            const channel = window.Echo.private(`pharmacies.${PHARM_ID}.orders`);

            const handleOrderEvent = (e) => {
                console.log('📡 Event commande reçu :', e);

                if (e.pharmacy_id !== null && e.pharmacy_id !== PHARM_ID) {
                    console.log('⏩ Commande pour une autre pharmacie, on ignore.');
                    return;
                }

                console.log('✅ Nouvelle commande pour cette pharmacie, on met à jour le compteur + alarme + toast.');

                // On force un refresh depuis le serveur pour ne pas avoir de décalage
                refreshPendingFromServer();
                showNewOrderToast(e.number ?? null);
            };

            channel
                .listen('.order.created', handleOrderEvent)
                .listen('OrderCreated', handleOrderEvent);
        }

        const ordersButtons = document.querySelectorAll('[data-orders-link]');
        ordersButtons.forEach((btn) => {
            btn.addEventListener('click', function () {
                this.classList.add('is-busy');
                const spinner = document.createElement('span');
                spinner.className = 'spinner';
                this.appendChild(spinner);
            });
        });
    });
</script>

<script>
  // ---- Contrôle automatique de présence toutes les 60 secondes (SANS redirection) ----
  (function () {
    const GEO_CHECK_URL   = "{{ route('pharmacist.geo.check') }}";
    const CSRF            = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const statusChip      = document.getElementById('presence-indicator');
    const lastCheckLabel  = document.getElementById('presence-last-check');

    function updateLastCheck() {
      if (!lastCheckLabel) return;
      const now = new Date();
      const hh  = String(now.getHours()).padStart(2,'0');
      const mm  = String(now.getMinutes()).padStart(2,'0');
      lastCheckLabel.textContent = 'Dernier contrôle : ' + hh + 'h' + mm;
    }

    function markOk() {
      if (statusChip) {
        const dot = statusChip.querySelector('span.dot');
        if (dot) {
          dot.style.background = '#22c55e';
          dot.style.boxShadow  = '0 0 8px rgba(34,197,94,.8)';
        }
        statusChip.lastElementChild.textContent =
          'Présence en pharmacie : OK';
      }
      updateLastCheck();
    }

    function markSuspicious(message) {
      if (statusChip) {
        const dot = statusChip.querySelector('span.dot');
        if (dot) {
          dot.style.background = '#f97316';
          dot.style.boxShadow  = '0 0 8px rgba(249,115,22,.8)';
        }
        statusChip.lastElementChild.textContent =
          'Présence en pharmacie : à vérifier (' + message + ')';
      }
      updateLastCheck();
    }

    function runPresenceCheck() {
      if (!navigator.geolocation) {
        markSuspicious('Géolocalisation non supportée');
        return;
      }

      navigator.geolocation.getCurrentPosition(
        function (pos) {
          const payload = {
            latitude:  pos.coords.latitude,
            longitude: pos.coords.longitude
          };

          fetch(GEO_CHECK_URL, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify(payload)
          })
          .then(r => r.json().then(data => ({ok: r.ok, status: r.status, body: data})))
          .then(res => {
            if (res.ok && res.body && res.body.status === 'ok') {
              markOk();
            } else {
              const msg = (res.body && res.body.message)
                ? res.body.message
                : 'Position non validée';
              markSuspicious(msg);
            }
          })
          .catch(() => {
            markSuspicious('Erreur réseau');
          });
        },
        function (err) {
          if (err.code === 1) {
            markSuspicious('Permission de localisation refusée');
          } else {
            markSuspicious('Erreur géoloc (code ' + err.code + ')');
          }
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
        }
      );
    }

    // Premier contrôle dès l’arrivée sur le dashboard
    runPresenceCheck();
    // Puis un contrôle toutes les 60 secondes
    setInterval(runPresenceCheck, 60_000);
  })();
</script>

<script>
  // 🔁 Rafraîchissement automatique du tableau de bord toutes les 5 secondes
  setInterval(function () {
      window.location.reload();
  }, 10000);
</script>

@vite(['resources/js/app.js'])
</body>
</html>
