<!DOCTYPE html>
<html lang="fr">
  <body style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#111">
    <p>Bonjour,</p>
    <p>Le compte <strong>Pharmacien</strong> a été créé pour la pharmacie <strong>{{ $pharmacy->name }}</strong>.</p>

    <p>Identifiants de connexion :</p>
    <ul>
      <li><b>Email :</b> {{ $user->email }}</li>
      <li><b>Mot de passe temporaire :</b> <code>{{ $plainPassword }}</code></li>
      <li><b>Expiration :</b> {{ $expiresAt->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</li>
    </ul>

    <p>👉 Connectez-vous sur <a href="{{ url('/login') }}">{{ url('/login') }}</a> avant l’expiration.<br>
       Après connexion, nous vous recommandons de définir un nouveau mot de passe.</p>

    <p>— PharmaOnline</p>
  </body>
</html>
