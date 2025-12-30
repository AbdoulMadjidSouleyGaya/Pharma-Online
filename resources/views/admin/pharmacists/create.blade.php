<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Créer un compte Pharmacien</title>
  <style>
    :root{--blue:#2b6cb0;--bg:#f3f6fb;--ink:#1f2937}
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink)}
    .wrap{max-width:700px;margin:24px auto;padding:0 16px}
    h1{margin:0 0 12px;font-size:28px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px}
    .row{margin:10px 0;display:flex;flex-direction:column}
    label{font-weight:600;margin-bottom:6px}
    select,input{padding:10px;border:1px solid #cbd5e1;border-radius:8px}
    .btn{appearance:none;border:1px solid #cbd5e1;background:#fff;color:#111827;border-radius:8px;padding:10px 14px;font-weight:600;cursor:pointer}
    .btn.primary{background:var(--blue);color:#fff;border-color:#1d4f91}
    .btn.primary:hover{filter:brightness(0.95)}
    .bar{display:flex;justify-content:space-between;align-items:center;margin:16px 0}
    .muted{color:#6b7280}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="bar">
      <h1>Créer un compte Pharmacien</h1>
      <a class="btn" href="{{ route('admin.pharmacists.index') }}">← Retour</a>
    </div>

    @if($errors->any())
      <div style="margin:10px 0;padding:10px;border:1px solid #ffd2d2;background:#fff0f0;border-radius:8px">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('admin.pharmacists.store') }}" class="card">
      @csrf

      <div class="row">
        <label for="pharmacy_id">Pharmacie</label>
        <select id="pharmacy_id" name="pharmacy_id" required>
          <option value="">— Sélectionner —</option>
          @foreach($pharmacies as $p)
            <option value="{{ $p->id }}" data-email="{{ $p->email ?? '' }}">{{ $p->name }}</option>
          @endforeach
        </select>
        <div class="muted">L’email et le nom seront remplis automatiquement.</div>
      </div>

      <div class="row">
        <label for="email">Email (pharmacie)</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required />
      </div>

      <div class="row">
        <label for="name">Nom du compte</label>
        <input id="name" name="name" type="text" value="{{ old('name','Pharmacien Principal') }}" required />
      </div>

      <div class="row" style="margin-top:16px">
        <button class="btn primary" type="submit">Créer le compte</button>
      </div>
    </form>
  </div>

  <script>
    const sel = document.getElementById('pharmacy_id');
    const email = document.getElementById('email');
    const nameInput = document.getElementById('name');

    sel?.addEventListener('change', () => {
      const opt = sel.options[sel.selectedIndex];
      const em = opt?.getAttribute('data-email') || '';
      if (em) email.value = em;
      if (!nameInput.value || nameInput.value === 'Pharmacien Principal') {
        nameInput.value = 'Pharmacien Principal';
      }
    });
  </script>
</body>
</html>
