<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Compte créé — PharmaOnline</title>
    <meta http-equiv="refresh" content="2;url={{ route('dashboard') }}">
    <style>
        body{
            margin:0; padding:0; min-height:100vh;
            display:flex; align-items:center; justify-content:center;
            background-image: url('{{ asset('images/jar-2338584_1280.jpeg') }}');
            background-size:cover; background-position:center;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color:#fff;
        }
        .container{
            background: rgba(0,0,0,.7);
            border-radius:12px; padding:32px 28px; width:90%; max-width:420px;
            text-align:center; box-shadow:0 4px 20px rgba(0,0,0,.5);
        }
        h1{ margin:0 0 10px; font-size:26px; }
        p{ margin:0 0 18px; font-size:16px; color:#e8e8e8; }
        a.button{
            display:inline-block; padding:12px 18px; font-weight:700; text-decoration:none;
            background:#007BFF; color:#fff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.2);
            transition: background .2s ease;
        }
        a.button:hover{ background:#0066d6; }
        .hint{ margin-top:8px; font-size:12px; color:#cfe6ff }
    </style>
</head>
<body>
    <div class="container">
        <h1>Compte créé avec succès 🎉</h1>
        <p>Bienvenue sur PharmaOnline. Vous allez être redirigé vers votre tableau de bord…</p>
        <a class="button" href="{{ route('dashboard') }}">Aller au tableau de bord</a>
        <div class="hint">(Redirection automatique dans quelques secondes)</div>
    </div>
</body>
</html>
