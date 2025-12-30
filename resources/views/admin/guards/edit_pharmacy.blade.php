<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — Modifier pharmacie de garde</title>
<style>
  :root{--bg:#0b1020;--ink:#fff;--muted:#a8b1c7;--stroke:#1f2937}
  *{box-sizing:border-box} body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:linear-gradient(180deg,#0b1020,#0a0f1f 60%,#0b0f1e);color:var(--ink)}
  .top{position:sticky;top:0;display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:linear-gradient(180deg,#0e1529,#0b1222);border-bottom:1px solid #0b1222}
  .btn{appearance:none;border:1px solid #1f2937;background:#111827;color:#fff;border-radius:10px;padding:8px 12px;text-decoration:none;font-weight:700}
  .btn:hover{background:#0f172a}
  .btn.green{background:#10b981;border-color:#128a66}.btn.green:hover{background:#0fa57b}
  .wrap{max-width:800px;margin:22px auto;padding:0 18px}
  .panel{background:linear-gradient(180deg,#0d1528,#0a1120);border:1px solid #1f2937;border-radius:16px;padding:16px}
  label{display:block;font-weight:700;margin:10px 2px 6px}
  input{width:100%;padding:10px;border:1px solid #334155;border-radius:10px;background:#0b1222;color:#e5e7eb}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  @media(max-width:840px){.row{grid-template-columns:1fr}}
</style>
</head>
<body>
  <div class="top">
    <a class="btn" href="{{ route('admin.guards.show', $schedule) }}">← Retour à la semaine</a>
  </div>

  <div class="wrap">
    <div class="panel">
      <h2 style="margin:0 0 8px">Modifier — {{ $pharmacy->name }}</h2>
      <form method="POST" action="{{ route('admin.guards.pharmacies.update', [$schedule, $pharmacy]) }}">
        @csrf @method('PUT')

        <label>Nom *</label>
        <input name="name" value="{{ old('name', $pharmacy->name) }}" required>

        <div class="row">
          <div>
            <label>Quartier</label>
            <input name="district" value="{{ old('district', $pharmacy->district) }}">
          </div>
          <div>
            <label>Contact</label>
            <input name="phone" value="{{ old('phone', $pharmacy->phone) }}">
          </div>
        </div>

        <label>Localisation</label>
        <input name="address" value="{{ old('address', $pharmacy->address) }}">

        <div style="margin-top:12px">
          <button class="btn green" type="submit">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
