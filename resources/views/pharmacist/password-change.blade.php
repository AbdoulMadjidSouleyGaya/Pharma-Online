<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Changer mon mot de passe</title>
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;background:#f6f7fb;margin:0}
    .wrap{max-width:480px;margin:48px auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px}
    h1{margin:0 0 12px;font-size:22px}
    .muted{color:#6b7280}
    .row{margin:10px 0}
    input{width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px}
    .btn{display:inline-block;margin-top:8px;padding:10px 14px;border:none;border-radius:10px;background:#2563eb;color:#fff;font-weight:700;cursor:pointer}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Changer mon mot de passe</h1>
    <p class="muted">Pour des raisons de sécurité, vous devez définir un nouveau mot de passe avant d’accéder à votre espace.</p>

    @if($errors->any())
      <div style="background:#fff0f0;border:1px solid #ffd2d2;padding:10px;border-radius:8px;margin:8px 0">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('pharmacist.password.change') }}">
      @csrf
      <div class="row">
        <label>Nouveau mot de passe</label>
        <input type="password" name="password" required autocomplete="new-password">
      </div>
      <div class="row">
        <label>Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" required autocomplete="new-password">
      </div>
      <button class="btn" type="submit">Valider</button>
    </form>
  </div>
</body>
</html>
