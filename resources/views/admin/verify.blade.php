<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PharmaOnline — Vérification administrateur</title>
  <style>
    :root{
      --bg:#0b1020; --card:#ffffff; --text:#111;
      --accent:#10b981; --ring:rgba(16,185,129,.35);
      --shadow:0 20px 60px rgba(0,0,0,.25);
    }
    *{box-sizing:border-box} html,body{height:100%}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:var(--text);background:var(--bg);overflow-x:hidden}
    .bg{position:fixed;inset:0;background:
      linear-gradient(180deg,rgba(0,0,0,.55),rgba(0,0,0,.6)),
      url('{{ asset('images/jar-2338584_1280.jpeg') }}') center/cover no-repeat}
    .blob{position:absolute;width:520px;height:520px;border-radius:999px;filter:blur(80px);opacity:.25}
    .blob.b1{top:-120px;left:-120px;background:#10b981}
    .blob.b2{bottom:-160px;right:-140px;background:#60a5fa}

    .wrap{position:relative;min-height:100dvh;display:grid;place-items:center;padding:24px}
    .card{width:100%;max-width:460px;background:var(--card);border-radius:20px;box-shadow:var(--shadow);overflow:hidden;border:1px solid #eef0f4}
    .head{display:flex;align-items:center;gap:12px;padding:20px 22px;background:linear-gradient(180deg,#ffffff,#fafafa);border-bottom:1px solid #eef0f4}
    .logo{width:42px;height:42px;object-fit:contain}
    .brand{font-size:15px;line-height:1.1}.brand b{display:block;font-size:16px}
    .content{padding:22px}
    .status,.error{padding:12px 14px;border-radius:12px;margin-bottom:12px;font-size:14px}
    .status{background:#e8f6ff;border:1px solid #bfe6ff;color:#0b4673}
    .error{background:#fff0f0;border:1px solid #ffd2d2;color:#7a1111}
    p.help{margin:0 0 8px;color:#374151;font-size:14px}

    .timers{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin:6px 0 12px}
    .badge{font-size:12px;padding:6px 10px;border-radius:999px;background:#f3f4f6;border:1px solid #e5e7eb;color:#111}
    .badge.warn{background:#fff7ed;border-color:#fed7aa;color:#9a3412}
    .badge.ok{background:#ecfdf5;border-color:#a7f3d0;color:#065f46}

    .otp{display:flex;gap:10px;justify-content:center;margin:12px 0 4px}
    .otp input{
      width:48px;height:56px;text-align:center;font-size:22px;font-weight:700;
      border:1px solid #e5e7eb;border-radius:12px;background:#fff;outline:none;
      transition:box-shadow .15s ease,border-color .15s ease; letter-spacing:1px;
    }
    .otp input:focus{border-color:var(--accent);box-shadow:0 0 0 4px var(--ring)}
    .hidden{display:none}

    .actions{display:flex;gap:10px;margin-top:14px}
    button, .btn{
      display:inline-flex;justify-content:center;align-items:center;gap:8px;
      padding:12px 14px;border:none;border-radius:14px;cursor:pointer;
      font-weight:700;font-size:15px;text-decoration:none;
      transition:background .15s ease, transform .03s ease;
    }
    .primary{background:#111;color:#fff;box-shadow:0 10px 30px rgba(0,0,0,.15)}
    .primary:hover{background:#000}.primary:active{transform:translateY(1px)}
    .ghost{background:#f3f4f6;color:#111;border:1px solid #e5e7eb}
    .ghost:hover{background:#edeef0}
    .ghost[disabled]{opacity:.6;cursor:not-allowed}

    .row{display:flex;justify-content:space-between;gap:8px;align-items:center;margin-top:10px}
    .link{color:#111;text-decoration:none;font-weight:600;font-size:14px}
    .link:hover{text-decoration:underline}

    .foot{padding:16px 22px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid #eef0f4;background:#fafafa}
    .hint{font-size:12px;color:#6b7280}
  </style>
</head>
<body>
  <div class="bg" aria-hidden="true"></div>
  <div class="blob b1"></div><div class="blob b2"></div>

  <div class="wrap">
    <div class="card">
      <div class="head">
        <img class="logo" src="{{ asset('images/logo.png') }}" alt="PharmaOnline">
        <div class="brand"><b>PharmaOnline</b><span>Vérification administrateur</span></div>
      </div>

      <div class="content">
        @if(session('status'))
          <div class="status">{{ session('status') }}</div>
        @endif

        @if($errors->any())
          <div class="error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
          </div>
        @endif

        <p class="help">
          Un code à 6 chiffres a été envoyé à <b>{{ auth()->user()->email }}</b>.
        </p>

        {{-- Badges compte à rebours --}}
        <div class="timers" id="timers"
             data-expires="{{ isset($expiresAt) ? $expiresAt->toIso8601String() : '' }}"
             data-sent="{{ isset($sentAt) ? $sentAt->toIso8601String() : '' }}">
          <span class="badge warn" id="ttlBadge">Expiration du code : —:—</span>
          <span class="badge" id="cooldownBadge">Renvoyer possible dans : —:—</span>
        </div>

        {{-- Formulaire principal : OTP --}}
        <form id="verifyForm" method="POST" action="{{ route('admin.verify.submit') }}">
          @csrf
          <div class="otp" id="otpGroup" aria-label="Entrer le code de vérification en 6 chiffres">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="one-time-code">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1">
          </div>
          <input class="hidden" type="text" name="code" id="code">

          <div class="actions">
            <button id="btnSubmit" type="submit" class="primary" style="flex:1">Valider le code</button>
          </div>
        </form>

        <div class="row">
          <form id="resendForm" method="POST" action="{{ route('admin.verify.resend') }}">
            @csrf
            <button id="btnResend" type="submit" class="ghost">Renvoyer un code</button>
          </form>

          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="ghost">Annuler</button>
          </form>
        </div>

        <div class="row" style="margin-top:8px">
          <a class="link" href="{{ route('admin.login') }}">← Revenir à la connexion</a>
          <span class="hint">Code valable 10 minutes</span>
        </div>
      </div>

      <div class="foot">
        <span class="hint">Besoin d’aide ? Contact : {{ auth()->user()->email }}</span>
        <a class="link" href="{{ route('home') }}">Accueil</a>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const inputs = Array.from(document.querySelectorAll('#otpGroup input'));
      const hidden = document.getElementById('code');
      const form = document.getElementById('verifyForm');
      const timers = document.getElementById('timers');
      const ttlBadge = document.getElementById('ttlBadge');
      const cooldownBadge = document.getElementById('cooldownBadge');
      const btnResend = document.getElementById('btnResend');
      const btnSubmit = document.getElementById('btnSubmit');

      // OTP UX
      inputs[0].focus();
      const onlyDigits = s => (s||'').replace(/\D/g,'');
      const syncHidden = () => hidden.value = inputs.map(i=>i.value).join('');

      inputs.forEach((inp, idx) => {
        inp.addEventListener('input', () => {
          inp.value = onlyDigits(inp.value).slice(0,1);
          if (inp.value && idx < inputs.length - 1) inputs[idx+1].focus();
          syncHidden();
        });
        inp.addEventListener('keydown', (e) => {
          if (e.key === 'Backspace' && !inp.value && idx > 0){
            inputs[idx-1].focus(); inputs[idx-1].value = ''; syncHidden();
          }
        });
        inp.addEventListener('paste', (e) => {
          const data = onlyDigits((e.clipboardData || window.clipboardData).getData('text'));
          if(!data) return;
          e.preventDefault();
          for (let i=0;i<inputs.length;i++){ inputs[i].value = data[i] || ''; }
          const next = Math.min(data.length, inputs.length)-1;
          inputs[Math.max(0,next)].focus();
          syncHidden();
        });
      });

      form.addEventListener('submit', (e) => {
        syncHidden();
        if (hidden.value.length !== 6){
          e.preventDefault();
          inputs[0].focus();
          alert('Veuillez saisir les 6 chiffres du code.');
        }
      });

      // Timers
      const expiresIso = timers?.dataset?.expires || '';
      const sentIso    = timers?.dataset?.sent    || '';
      const expiresAt  = expiresIso ? new Date(expiresIso) : null;
      const sentAt     = sentIso    ? new Date(sentIso)    : null;

      function fmt(ms){
        if (ms < 0) ms = 0;
        const s = Math.floor(ms/1000);
        const m = Math.floor(s/60);
        const r = s % 60;
        return `${String(m).padStart(2,'0')}:${String(r).padStart(2,'0')}`;
      }

      function tick(){
        const now = new Date();

        // 10 minutes d’expiration
        if (expiresAt){
          const ttl = expiresAt - now;
          ttlBadge.textContent = `Expiration du code : ${fmt(ttl)}`;
          if (ttl <= 0){
            ttlBadge.className = 'badge warn';
            ttlBadge.textContent = 'Code expiré — renvoyez un nouveau code';
            btnSubmit.disabled = true;
            inputs.forEach(i => i.disabled = true);
          }
        }

        // Cooldown 60s pour renvoi
        if (sentAt){
          const cooldownEnd = new Date(sentAt.getTime() + 60*1000);
          const rest = cooldownEnd - now;
          if (rest > 0){
            btnResend.disabled = true;
            cooldownBadge.textContent = `Renvoyer possible dans : ${fmt(rest)}`;
            cooldownBadge.className = 'badge';
          } else {
            btnResend.disabled = false;
            cooldownBadge.textContent = 'Vous pouvez renvoyer un code';
            cooldownBadge.className = 'badge ok';
          }
        }
      }

      tick();
      setInterval(tick, 1000);
    })();
  </script>
</body>
</html>
