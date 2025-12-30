<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Modifier une pharmacie</title>
  <style>
    :root{--bg:#0b1020;--ink:#fff;--muted:#a8b1c7;--stroke:#1f2937;--shadow:0 30px 60px rgba(0,0,0,.35)}
    *{box-sizing:border-box} body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:linear-gradient(180deg,#0b1020,#0a0f1f 60%,#0b0f1e);color:var(--ink)}
    .top{position:sticky;top:0;display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:linear-gradient(180deg,#0e1529,#0b1222);border-bottom:1px solid #0b1222;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    .btn{appearance:none;border:1px solid #1f2937;background:#111827;color:#fff;border-radius:10px;padding:8px 12px;text-decoration:none;font-weight:700}
    .btn:hover{background:#0f172a}
    .btn.green{background:#10b981;border-color:#128a66}.btn.green:hover{background:#0fa57b}
    .wrap{max-width:900px;margin:22px auto;padding:0 18px}
    .panel{background:linear-gradient(180deg,#0d1528,#0a1120);border:1px solid var(--stroke);border-radius:16px;padding:16px;box-shadow:var(--shadow)}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:840px){.row{grid-template-columns:1fr}}
    label{display:block;font-weight:700;margin:10px 2px 6px}
    input,textarea{width:100%;padding:10px;border:1px solid #334155;border-radius:10px;background:#0b1222;color:#e5e7eb}
    .muted{color:var(--muted);font-size:13px}
    .err{background:#fff0f0;border:1px solid #ffd2d2;color:#7a1111;padding:10px 12px;border-radius:10px;margin-bottom:12px}
  </style>
</head>
<body>
  <div class="top">
    <a class="btn" href="{{ route('admin.pharmacies.index') }}">← Retour à la liste</a>
    <div></div>
  </div>

  <div class="wrap">
    <div class="panel">
      <h2 style="margin:0 0 8px">Modifier la pharmacie</h2>
      <p class="muted">Mettez à jour les informations puis enregistrez.</p>

      @if ($errors->any())
        <div class="err">@foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach</div>
      @endif

      <form method="POST" action="{{ route('admin.pharmacies.update', $pharmacy) }}">
        @csrf @method('PUT')

        <div class="row">
          <div>
            <label>Nom *</label>
            <input type="text" name="name" value="{{ old('name', $pharmacy->name) }}" required>
          </div>
          <div>
            <label>Quartier *</label>
            <input type="text" name="district" value="{{ old('district', $pharmacy->district) }}" required>
          </div>
        </div>

        <label>Adresse</label>
        <input type="text" name="address" value="{{ old('address', $pharmacy->address) }}">

        <div class="row">
          <div>
            <label>Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone', $pharmacy->phone) }}">
          </div>
          <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $pharmacy->email) }}">
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:14px">
          <button class="btn green" type="submit">Enregistrer</button>
          <a class="btn" href="{{ route('admin.pharmacies.index') }}">Annuler</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
