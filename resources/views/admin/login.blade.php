<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PharmaOnline — Espace Administrateur</title>
  <style>
    :root{
      --bg:#0b1020; --card:#ffffff; --text:#111;
      --primary:#111; --primary-h:#000;
      --muted:#6b7280; --accent:#10b981;
      --ring:rgba(16,185,129,.35);
      --shadow:0 20px 60px rgba(0,0,0,.25);
    }
    *{box-sizing:border-box} html,body{height:100%}
    body{
      margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--text); background:var(--bg); overflow-x:hidden;
    }
    .bg{
      position:fixed; inset:0; background:
        linear-gradient(180deg, rgba(0,0,0,.55), rgba(0,0,0,.6)),
        url('{{ asset('images/jar-2338584_1280.jpeg') }}') center/cover no-repeat;
      filter: saturate(1.05);
    }
    .blob{position:absolute; width:520px; height:520px; border-radius:999px; filter: blur(80px); opacity:.25}
    .blob.b1{top:-120px; left:-120px; background:#10b981}
    .blob.b2{bottom:-160px; right:-140px; background:#60a5fa}

    .wrap{
      position:relative; min-height:100dvh; display:grid; place-items:center; padding:24px;
    }
    .card{
      width:100%; max-width:420px; background:var(--card);
      border-radius:20px; box-shadow:var(--shadow); overflow:hidden; border:1px solid #eef0f4;
    }
    .head{
      display:flex; align-items:center; gap:12px; padding:20px 22px; background:linear-gradient(180deg,#ffffff,#fafafa);
      border-bottom:1px solid #eef0f4;
    }
    .logo{width:42px; height:42px; object-fit:contain}
    .brand{font-size:15px; line-height:1.1}
    .brand b{display:block; font-size:16px}
    .content{padding:22px}
    .status,.error{
      padding:12px 14px; border-radius:12px; margin-bottom:12px; font-size:14px
    }
    .status{background:#e8f6ff; border:1px solid #bfe6ff; color:#0b4673}
    .error{background:#fff0f0; border:1px solid #ffd2d2; color:#7a1111}
    label{display:block; font-weight:600; font-size:14px; margin:10px 2px 6px}
    .field{
      position:relative;
    }
    input{
      width:100%; padding:12px 44px 12px 14px; border:1px solid #e5e7eb; border-radius:12px; font-size:15px; outline:none;
      transition: box-shadow .15s ease, border-color .15s ease; background:#fff;
    }
    input:focus{border-color:var(--accent); box-shadow:0 0 0 4px var(--ring)}
    .toggle{
      position:absolute; right:8px; top:50%; transform:translateY(-50%);
      background:#f3f4f6; border:1px solid #e5e7eb; padding:6px 10px; border-radius:10px; font-size:12px; cursor:pointer;
    }
    button[type="submit"]{
      width:100%; margin-top:14px; padding:12px 16px; border:none; border-radius:14px; cursor:pointer;
      background:var(--primary); color:#fff; font-weight:700; font-size:15px;
      box-shadow: 0 10px 30px rgba(0,0,0,.15);
      transition: background .15s ease, transform .03s ease;
    }
    button[type="submit"]:hover{background:var(--primary-h)}
    button[type="submit"]:active{transform: translateY(1px)}
    .row{display:flex; justify-content:space-between; gap:8px; margin-top:10px; align-items:center}
    .link{color:#111; text-decoration:none; font-weight:600; font-size:14px}
    .link:hover{text-decoration:underline}
    .foot{padding:16px 22px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid #eef0f4; background:#fafafa}
    .hint{font-size:12px; color:var(--muted)}
  </style>
</head>
<body>
  <div class="bg" aria-hidden="true"></div>
  <div class="blob b1"></div><div class="blob b2"></div>

  <div class="wrap">
    <div class="card">
      <div class="head">
        <img class="logo" src="{{ asset('images/logo.png') }}" alt="PharmaOnline">
        <div class="brand"><b>PharmaOnline</b><span>Espace Administrateur</span></div>
      </div>

      <div class="content">
        @if (session('status'))
          <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="error">
            @foreach ($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
          @csrf

          <label for="email">Email administrateur</label>
          <div class="field">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@exemple.com">
          </div>

          <label for="password">Mot de passe</label>
          <div class="field">
            <input id="password" type="password" name="password" required placeholder="••••••••">
            <button class="toggle" type="button" onclick="togglePwd()">Voir</button>
          </div>

          <button type="submit">Se connecter</button>

          <div class="row">
            <a class="link" href="{{ route('home') }}">← Retour à l’accueil</a>
            <span class="hint">Connexion en 2 étapes par email</span>
          </div>
        </form>
      </div>

      <div class="foot">
        <span class="hint">Besoin d’aide ? Contact : uzumakinarutoniger@gmail.com</span>
        <a class="link" href="{{ route('admin.login') }}">Admin</a>
      </div>
    </div>
  </div>

  <script>
    function togglePwd(){
      const input = document.getElementById('password');
      const btn = event.currentTarget;
      if (input.type === 'password'){ input.type = 'text'; btn.textContent = 'Cacher'; }
      else { input.type = 'password'; btn.textContent = 'Voir'; }
    }
  </script>
</body>
</html>
