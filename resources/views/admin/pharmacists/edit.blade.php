<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Modifier un Pharmacien</title>

  <style>
    :root{
      --blue:#2563eb;
      --blue-dark:#1e40af;
      --bg:#eef2f7;
      --ink:#1f2937;
      --red:#b91c1c;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:var(--bg);
      color:var(--ink);
    }
    .wrap{
      max-width:700px;
      margin:26px auto;
      padding:0 18px;
    }
    h1{
      margin:0 0 4px;
      font-size:26px;
      font-weight:700;
      color:#111827;
    }
    .subtitle{
      margin:0 0 18px;
      font-size:14px;
      color:#6b7280;
    }

    .card{
      background:#fff;
      border-radius:12px;
      border:1px solid #d0d7e2;
      padding:18px 18px 20px;
      box-shadow:0 1px 2px rgba(0,0,0,0.06);
    }

    .field{
      margin-bottom:14px;
    }
    label{
      display:block;
      font-size:13px;
      font-weight:600;
      color:#374151;
      margin-bottom:4px;
    }
    .input{
      width:100%;
      padding:8px 10px;
      border-radius:8px;
      border:1px solid #cbd5e1;
      font-size:14px;
    }

    .help{
      font-size:12px;
      color:#6b7280;
      margin-top:4px;
    }

    .error{
      font-size:12px;
      color:#b91c1c;
      margin-top:3px;
    }

    .actions{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      margin-top:16px;
    }

    .btn{
      appearance:none;
      border:1px solid #cbd5e1;
      border-radius:8px;
      padding:7px 12px;
      font-size:14px;
      font-weight:600;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      gap:6px;
      background:#fff;
      color:#111827;
      text-decoration:none;
      transition:0.2s;
    }
    .btn:hover{background:#f9fafb}

    .btn.blue{
      background:var(--blue);
      border-color:var(--blue);
      color:#fff;
    }
    .btn.blue:hover{background:var(--blue-dark)}

    .badge{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:3px 8px;
      border-radius:999px;
      font-size:11px;
      background:#e5e7eb;
      color:#374151;
    }
  </style>
</head>
<body>
  <div class="wrap">

    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:20px;">
      <div>
        <h1>Modifier un Pharmacien</h1>
        <p class="subtitle">
          <span class="badge">
            👤 {{ $pharmacist->name }}
          </span>
          &nbsp; &lt;{{ $pharmacist->email }}&gt;
        </p>
      </div>

      <a href="{{ route('admin.pharmacists.index') }}" class="btn">
        ⬅️ Retour à la liste
      </a>
    </div>

    @if($errors->any())
      <div style="margin-bottom:12px;padding:10px;border-radius:8px;border:1px solid #fecaca;background:#fef2f2;color:#7f1d1d;font-size:13px;">
        @foreach($errors->all() as $e)
          <div>• {{ $e }}</div>
        @endforeach
      </div>
    @endif

    <div class="card">
      <form method="POST" action="{{ route('admin.pharmacists.update', $pharmacist) }}">
        @csrf
        @method('PUT')

        <div class="field">
          <label for="name">Nom complet</label>
          <input id="name" name="name" type="text" class="input"
                 value="{{ old('name', $pharmacist->name) }}" required>
          @error('name')
            <div class="error">{{ $message }}</div>
          @enderror
        </div>

        <div class="field">
          <label for="email">Adresse email</label>
          <input id="email" name="email" type="email" class="input"
                 value="{{ old('email', $pharmacist->email) }}" required>
          @error('email')
            <div class="error">{{ $message }}</div>
          @enderror
        </div>

        <div class="field">
          <label for="password">Nouveau mot de passe (optionnel)</label>
          <input id="password" name="password" type="password" class="input" autocomplete="new-password">
          <div class="help">
            Laisse vide si tu ne veux pas changer le mot de passe.
          </div>
          @error('password')
            <div class="error">{{ $message }}</div>
          @enderror
        </div>

        <div class="actions">
          <a href="{{ route('admin.pharmacists.index') }}" class="btn">
            Annuler
          </a>
          <button type="submit" class="btn blue">
            💾 Enregistrer les modifications
          </button>
        </div>

      </form>
    </div>
  </div>
</body>
</html>
