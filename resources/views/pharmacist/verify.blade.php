<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>PharmaOnline — Vérification 2FA Pharmacien</title>
  <style>
    :root{
      --brand:#2563eb; --brand-2:#1e40af;
      --bg:#f6f7fb; --card:#ffffff; --line:#e5e7eb;
      --ink:#0f172a; --muted:#6b7280;
      --ok:#10b981; --warn:#f59e0b; --danger:#ef4444;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;background:var(--bg);color:var(--ink);font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
    .wrap{max-width:560px;margin:34px auto;padding:0 16px}
    .card{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:22px}
    h1{margin:0 0 8px;font-size:24px}
    p{margin:8px 0}
    .muted{color:var(--muted)}
    .flash{margin:10px 0;padding:12px;border-radius:10px;border:1px solid}
    .flash.ok{border-color:#c7eed8;background:#ecfdf5}
    .flash.err{border-color:#ffd2d2;background:#fff0f0}
    .flash.warn{border-color:#fde68a;background:#fffbeb}
    .code-grid{display:grid;grid-template-columns:repeat(8,1fr);gap:10px;margin:16px 0}
    .digit{
      width:100%; aspect-ratio:1/1.1; text-align:center; font-size:24px; font-weight:800;
      border:1px solid var(--line); border-radius:10px; outline:none; background:#f9fafb;
    }
    .digit:focus{border-color:var(--brand); box-shadow:0 0 0 3px rgba(37,99,235,.15)}
    .btn{
      appearance:none;border:1px solid #cbd5e1;background:#fff;border-radius:10px;
      padding:10px 14px;font-weight:700;cursor:pointer
    }
    .btn.primary{background:var(--brand);border-color:var(--brand-2);color:#fff}
    .btn.primary:hover{filter:brightness(.98)}
    .btn.gray{background:#fff;color:var(--ink)}
    .btn.gray:hover{background:#f1f5f9}
    .row{display:flex;gap:10px;align-items:center}
    .row-between{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
    .timer{font-variant-numeric:tabular-nums}
    .top{
      background:linear-gradient(90deg,var(--brand),var(--brand-2)); color:#fff; border-bottom:1px solid #ffffff33;
      padding:12px 0; margin-bottom:18px;
    }
    .top .inner{max-width:560px;margin:0 auto;padding:0 16px;display:flex;justify-content:space-between;align-items:center}
    a.brand{display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none;font-weight:800}
    .brand-badge{width:34px;height:34px;border-radius:10px;background:#fff1;display:grid;place-items:center;border:1px solid #ffffff33}
    .help{margin-top:12px}
  </style>
</head>
<body>

  <div class="top">
    <div class="inner">
      <a href="{{ url('/') }}" class="brand">
        <span class="brand-badge">💊</span> <span>PharmaOnline — Espace Pharmacien</span>
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn gray" type="submit">Se déconnecter</button>
      </form>
    </div>
  </div>

  <div class="wrap">
    @if(session('status')) <div class="flash ok">{{ session('status') }}</div> @endif
    @if($errors->any())
      <div class="flash err">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
    @endif

    <div class="card">
      <h1>Vérification en deux étapes</h1>
      @php
        $user = auth()->user();
        $email = $user?->email;
        // Essaie d’obtenir une date d’expiration si le contrôleur la passe
        $expiresAt = $expiresAt ?? session('pharmacist_2fa_expires_at') ?? null;
        $secondsLeft = 0;
        if ($expiresAt) {
          try {
            $secondsLeft = max(0, \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($expiresAt), false));
          } catch (\Throwable $e) { $secondsLeft = 0; }
        }
        // petit masque d’email
        $masked = $email ? preg_replace('/(^..).+(@.+$)/', '$1***$2', $email) : '—';
      @endphp

      <p class="muted">Un code à <strong>8 chiffres</strong> a été envoyé à <strong>{{ $masked }}</strong>. Saisissez-le pour accéder à votre tableau de bord.</p>

      <form id="verifyForm" method="POST" action="{{ route('pharmacist.verify.submit') }}" autocomplete="off">
        @csrf
        <input type="hidden" name="code" id="codeHidden">
        <div class="code-grid">
          @for($i=0; $i<8; $i++)
            <input class="digit" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Chiffre {{ $i+1 }}">
          @endfor
        </div>

        <div class="row-between">
          <button class="btn primary" type="submit">Valider le code</button>

          <div class="row">
            <form id="resendForm" method="POST" action="{{ route('pharmacist.verify.resend') }}">
              @csrf
              <button id="resendBtn" class="btn gray" type="submit">Renvoyer le code</button>
            </form>
            <span id="timer" class="muted timer" style="display:none"></span>
          </div>
        </div>
      </form>

      <div class="help muted">
        <strong>Besoin d’aide ?</strong> Vérifiez vos spams. Si vous ne recevez rien, renvoyez un code ou contactez le support.
      </div>
    </div>
  </div>

  <script>
    // ——— Gestion des 8 cases + collage ———
    const inputs = Array.from(document.querySelectorAll('.digit'));
    const hidden = document.getElementById('codeHidden');
    const form   = document.getElementById('verifyForm');

    inputs.forEach((inp, idx) => {
      inp.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g,'').slice(0,1);
        if (e.target.value && idx < inputs.length-1) inputs[idx+1].focus();
        sync();
      });
      inp.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !e.target.value && idx>0) inputs[idx-1].focus();
      });
      inp.addEventListener('paste', e => {
        e.preventDefault();
        const txt = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,8);
        for (let i=0;i<inputs.length;i++) inputs[i].value = txt[i] || '';
        (txt.length>=8 ? inputs[7] : inputs[Math.min(txt.length,7)]).focus();
        sync();
      });
    });

    function sync(){
      hidden.value = inputs.map(i=>i.value || '').join('');
    }

    form.addEventListener('submit', e => {
      sync();
      if (hidden.value.length !== 8) {
        e.preventDefault();
        alert('Veuillez saisir les 8 chiffres du code.');
      }
    });

    // ——— Compte à rebours pour le renvoi du code ———
    const secondsInit = {{ (int) $secondsLeft }};
    const resendBtn = document.getElementById('resendBtn');
    const timerEl   = document.getElementById('timer');

    function fmt(s){
      const m = Math.floor(s/60), r = s%60;
      return `${String(m).padStart(2,'0')}:${String(r).padStart(2,'0')}`;
    }

    let left = secondsInit;
    if (left > 0){
      resendBtn.disabled = true;
      timerEl.style.display = 'inline';
      timerEl.textContent = `Nouvelle tentative dans ${fmt(left)}`;
      const iv = setInterval(()=>{
        left--;
        if (left <= 0){
          clearInterval(iv);
          resendBtn.disabled = false;
          timerEl.style.display = 'none';
        } else {
          timerEl.textContent = `Nouvelle tentative dans ${fmt(left)}`;
        }
      }, 1000);
    }
  </script>
</body>
</html>
