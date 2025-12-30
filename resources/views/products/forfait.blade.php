<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Forfaits — PharmaOnline</title>
  <style>
    :root{
      --primary:#2563eb; --primary-2:#1d4ed8;
      --bg:#f6f8fc; --panel:#fff; --ink:#0f172a; --muted:#64748b; --border:#e5e7eb;
      --ok:#10b981; --warn:#f59e0b; --dark:#0b1220;
    }
    *{box-sizing:border-box}
    html,body{margin:0;background:var(--bg);color:var(--ink);font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}

    /* Topbar */
    .topbar{position:sticky;top:0;z-index:10;background:linear-gradient(90deg,#1e3a8a 0%, #2563eb 50%, #60a5fa 100%);color:#fff;border-bottom:1px solid #ffffff22}
    .topbar-in{max-width:1200px;margin:0 auto;padding:12px 16px;display:flex;align-items:center;gap:16px;justify-content:space-between}
    .brand{display:flex;align-items:center;gap:10px;font-weight:800;letter-spacing:.3px}
    .brand .logo{width:28px;height:28px;border-radius:8px;background:#fff1;display:grid;place-items:center;border:1px solid #ffffff33}
    .nav{display:flex;gap:10px;align-items:center}
    .link{color:#fff;text-decoration:none;font-weight:600;padding:8px 10px;border-radius:8px;border:1px solid transparent}
    .link:hover{background:#ffffff12;border-color:#ffffff2a}

    /* Shell */
    .wrap{max-width:1200px;margin:28px auto;padding:0 16px;display:grid;grid-template-columns: 7fr 5fr;gap:18px}
    @media(max-width:1000px){ .wrap{grid-template-columns:1fr} }

    /* Panels */
    .panel{background:var(--panel);border:1px solid var(--border);border-radius:14px}
    .head{padding:16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center}
    .head h1,.head h2{margin:0;font-size:20px}
    .body{padding:16px}

    /* Plan cards */
    .plans{display:grid;grid-template-columns:repeat(12,1fr);gap:14px}
    .plan{grid-column:span 6;border:1px solid var(--border);border-radius:14px;padding:16px;display:flex;flex-direction:column;gap:10px}
    .plan h3{margin:0;font-size:18px}
    .price{font-weight:900;font-size:22px}
    .muted{color:var(--muted)}
    .ul{margin:8px 0 0;padding-left:18px}
    .ul li{margin:6px 0}
    .badge{display:inline-block;font-size:12px;font-weight:800;padding:4px 8px;border-radius:999px;background:#eef2ff;color:#1e40af}
    .cta{margin-top:auto;display:flex;gap:8px}
    .btn{appearance:none;border:1px solid #cbd5e1;background:#fff;border-radius:10px;padding:10px 14px;font-weight:700;text-decoration:none;color:#111827;cursor:pointer;display:inline-flex;align-items:center;gap:8px;justify-content:center}
    .btn:hover{background:#f8fafc}
    .btn.primary{background:var(--primary);border-color:var(--primary-2);color:#fff}
    .btn.primary:hover{filter:brightness(1.03)}
    .note{margin-top:8px;font-size:12px;color:var(--muted)}

    /* Form */
    .form .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:720px){ .form .grid{grid-template-columns:1fr} }
    .field{display:flex;flex-direction:column;gap:6px}
    label{font-weight:700}
    select,input{padding:10px 12px;border:1px solid var(--border);border-radius:10px;background:#fff}
    .help{font-size:12px;color:var(--muted)}
    .sum{background:#0b1220;color:#fff;border-radius:12px;padding:14px}
    .sum .row{display:flex;justify-content:space-between;align-items:center;margin:6px 0}
    .sum .big{font-size:26px;font-weight:900}
    .foot{display:flex;justify-content:flex-end;gap:10px;margin-top:12px}

    .chip{display:inline-flex;align-items:center;gap:8px;padding:7px 10px;border-radius:999px;background:#f1f5f9;border:1px solid #e2e8f0;font-weight:700}
  </style>
</head>
<body>

<header class="topbar">
  <div class="topbar-in">
    <div class="brand">
      <div class="logo">Rx</div>
      <div>PharmaOnline</div>
    </div>
    <nav class="nav">
      <a class="link" href="{{ route('dashboard') }}">Mon tableau de bord</a>
      <a class="link" href="{{ route('pharmacies.index') }}">Pharmacies</a>
      <a class="link" href="{{ url('/pharmacies-de-garde') }}">Pharmacies de garde</a>
    </nav>
  </div>
</header>

<main class="wrap">

  <!-- COLONNE GAUCHE : Cartes des plans (sans Essentiel) -->
  <section class="panel">
    <div class="head">
      <h2>Choisissez votre Pack</h2>
      <span class="chip">Support 7j/7</span>
    </div>
    <div class="body">
      <div class="plans">
      
        <!-- Standard -->
        <div class="plan">
          <span class="badge">Populaire</span>
          <h3>Standard</h3>
          <div class="price">25 FCFA/j</div>
          <ul class="ul">
            <li>Fonctionnalités de base indispensables</li>
            <li>Recherche de produits pharmaceutiques</li>
            <li>Connaître la disponibilité du produit</li>
            <li>Affichage du prix du produit recherché</li>
          </ul>
          <div class="cta">
            <button class="btn primary" type="button" onclick="selectPlan('Standard')">Choisir Standard</button>
          </div>
          <div class="note">Équilibre idéal fonctionnalités / prix.</div>
        </div>

        <!-- Premium -->
        <div class="plan">
          <span class="badge">Complet</span>
          <h3>Premium</h3>
          <div class="price">50 FCFA/j</div>
          <ul class="ul">
            <li>Toutes les fonctionnalités de Standard</li>
            <li>Lancer la commande auprès de la pharmacie</li>
          </ul>
          <div class="cta">
            <button class="btn" type="button" onclick="selectPlan('Premium')">Choisir Premium</button>
          </div>
          <div class="note">Pour un parcours 100% fluide.</div>
        </div>

      </div>
    </div>
  </section>

  <!-- COLONNE DROITE : Formulaire -->
  <section class="panel">
    <div class="head">
      <h2>Configurer mon forfait</h2>
      <span class="chip">Sélection sécurisée</span>
    </div>
    <div class="body form">
    <div class="body form">
    @if ($errors->any())
      <div style="margin-bottom:12px;padding:10px;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#7f1d1d">
        @foreach($errors->all() as $e)
          <div>• {{ $e }}</div>
        @endforeach
      </div>
    @endif

    @if (session('forfait_ok'))
      @php $pp = session('forfait_payload'); @endphp
      <div style="margin-bottom:12px;padding:10px;border-radius:10px;background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46">
        <strong>{{ session('forfait_ok') }}</strong><br>
        Pack: <b>{{ $pp['plan'] ?? '—' }}</b> —
        Durée: <b>{{ $pp['days'] ?? '—' }} jours</b> —
        Opérateur: <b>{{ $pp['operator'] ?? '—' }}</b> —
        Contact: <b>{{ $pp['contact'] ?? '—' }}</b> —
        Total: <b>{{ $pp['price'] ?? '—' }} FCFA</b>
      </div>
    @endif
      {{-- Utilise GET tant que la route de paiement n’est pas prête --}}
      <form action="{{ route('products.search') }}" method="GET">

        <div class="grid">
          <div class="field">
            <label for="plan">Pack</label>
            <select id="plan" name="plan" onchange="updatePrice()">
              <option value="Standard" selected>Standard</option>
              <option value="Premium">Premium</option>
            </select>
            <div class="help">Choisissez le pack qui vous convient.</div>
          </div>

          <div class="field">
            <label for="duree">Durée du forfait</label>
            <select id="duree" name="duree" onchange="updatePrice()">
              <option value="Journalier">Journalier (1 jour)</option>
              <option value="Standard" selected>Semaine (7 jours)</option>
              <option value="Mensuel">Mensuel (30 jours)</option>
              <option value="Annuel">Annuel (365 jours)</option>
            </select>
            <div class="help">Les prix s’ajustent automatiquement.</div>
          </div>

          <div class="field">
            <label for="paiement">Choisir votre opérateur</label>
            <select id="paiement" name="operator">
              <option value="airtel">Airtel Niger</option>
              <option value="moov">Moov Africa Niger</option>
              <option value="zamani">Zamani Telecom</option>
              <option value="nigertelecom">Niger Telecom</option>
            </select>
            <div class="help">Sélectionnez votre opérateur téléphonique.</div>
          </div>

          <div class="field">
            <label for="contact">Contact</label>
            <input id="contact" name="contact" type="tel" placeholder="+227 …" value="{{ old('contact') }}">
            <div class="help">Votre numéro pour la confirmation.</div>
          </div>
        </div>

        <div style="margin-top:12px;display:grid;grid-template-columns:1fr;gap:12px">
          <div class="sum">
            <div class="row">
              <div>Pack sélectionné</div>
              <div id="sel-plan"><strong>Standard</strong></div>
            </div>
            <div class="row">
              <div>Durée</div>
              <div id="sel-duree"><strong>7 jours</strong></div>
            </div>
            <div class="row">
              <div>Total</div>
              <div class="big" id="price">— FCFA</div>
            </div>
          </div>

          <div class="foot">
            <a class="btn" href="{{ route('dashboard') }}">Annuler</a>
            <button class="btn primary" type="submit" onclick="resetPrice()">Valider le forfait</button>
          </div>
        </div>

      </form>
    </div>
  </section>

</main>

<script>
@php
    // Grilles tarifaires (Essentiel retiré)
    $PRICING = ($pricing ?? [
        'Standard'  => ['Journalier' => 25, 'Standard' => 150, 'Mensuel' => 600,  'Annuel' => 6000],
        'Premium'   => ['Journalier' => 50, 'Standard' => 300, 'Mensuel' => 1200, 'Annuel' => 12000],
    ]);
@endphp
  const PRICING = @json($PRICING);

  // libellés lisibles pour la durée
  const DUREE_LABELS = {
    'Journalier': '1 jour',
    'Standard'  : '7 jours',
    'Mensuel'   : '30 jours',
    'Annuel'    : '365 jours',
  };

  const planSel   = document.getElementById('plan');
  const dureeSel  = document.getElementById('duree');
  const priceEl   = document.getElementById('price');
  const selPlanEl = document.getElementById('sel-plan');
  const selDurEl  = document.getElementById('sel-duree');

  function updatePrice(){
    const plan  = planSel?.value;
    const duree = dureeSel?.value;

    if (selPlanEl) selPlanEl.innerHTML = "<strong>"+(plan||'—')+"</strong>";
    if (selDurEl)  selDurEl.innerHTML  = "<strong>"+(DUREE_LABELS[duree] || '—')+"</strong>";

    if (plan && duree && PRICING[plan] && (duree in PRICING[plan])) {
      priceEl.textContent = PRICING[plan][duree] + ' FCFA';
    } else {
      priceEl.textContent = '— FCFA';
    }
  }

  function selectPlan(planName){
    if (planSel) {
      planSel.value = planName;
      planSel.dispatchEvent(new Event('change'));
    }
    const formPanel = document.querySelector('.panel .form');
    if (formPanel) formPanel.scrollIntoView({behavior:'smooth', block:'start'});
  }

  function resetPrice(){ setTimeout(updatePrice, 0); }

  planSel?.addEventListener('change', updatePrice);
  dureeSel?.addEventListener('change', updatePrice);
  updatePrice();
</script>

</body>
</html>