<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Configuration — Pharmacies de garde</title>
  <style>
    *{box-sizing:border-box} body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;background:#0b1020;color:#fff}
    .wrap{max-width:1000px;margin:24px auto;padding:0 16px}
    .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
    .btn{appearance:none;border:1px solid #1f2937;background:#111827;color:#fff;border-radius:10px;padding:10px 14px;font-weight:800;text-decoration:none;cursor:pointer}
    .btn:hover{background:#0f172a}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:840px){.grid{grid-template-columns:1fr}}
    .card{background:linear-gradient(180deg,#0d1528,#0a1120);border:1px solid #1f2937;border-radius:16px;padding:16px}
    label{display:block;font-weight:800;margin:10px 0 6px}
    input,select{width:100%;padding:10px;border:1px solid #334155;border-radius:10px;background:#0b1222;color:#e5e7eb}
    .muted{color:#a8b1c7;font-size:14px}
    .ok{background:#0b2a1f;border:1px solid #14532d;color:#d1fae5;padding:10px;border-radius:10px;margin-bottom:12px}
    .err{background:#2a0b0b;border:1px solid #7f1d1d;color:#fee2e2;padding:10px;border-radius:10px;margin-bottom:12px}
    .hr{height:1px;background:#1f2937;margin:16px 0}
  </style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <a class="btn" href="{{ route('admin.guards.index') }}">← Retour</a>
    <div class="muted">Rotation automatique (samedi 13h → samedi 13h)</div>
  </div>

  @if(session('status')) <div class="ok">{{ session('status') }}</div> @endif
  @if($errors->any())
    <div class="err">
      @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
    </div>
  @endif

  <div class="card">
    <h2 style="margin:0 0 8px">1) Configuration de la rotation</h2>

    <form method="POST" action="{{ route('admin.guards.store') }}">
      @csrf
      <div class="grid">
        <div>
          <label>Région (optionnel)</label>
          <input name="region" value="{{ old('region', $cfg->region ?? 'Niamey') }}" placeholder="Niamey">
        </div>

        <div>
          <label>Groupe de départ *</label>
          <select name="start_group" required>
            @for($i=1;$i<=5;$i++)
              <option value="{{ $i }}" @selected((int)old('start_group', $cfg->start_group ?? 2) === $i)>
                Groupe {{ $i }}
              </option>
            @endfor
          </select>
        </div>
      </div>

      <label>Date/heure de départ officielle *</label>
      <input type="datetime-local" name="rotation_start_at"
             value="{{ old('rotation_start_at', optional($cfg->rotation_start_at ?? null)?->format('Y-m-d\TH:i')) }}"
             required>

      <div class="muted" style="margin-top:8px">
        Exemple : 13/12/2025 13:00 (début Groupe 2)
      </div>

      <div style="margin-top:14px">
        <button class="btn" type="submit">Enregistrer la configuration</button>
      </div>
    </form>

    <div class="hr"></div>

    <h2 style="margin:0 0 8px">2) Import des listes des 5 groupes (Excel/CSV)</h2>
    <div class="muted" style="margin-bottom:10px">
      Importer une fois par groupe. Le système affichera ensuite automatiquement le bon groupe chaque semaine.
    </div>

    <div class="grid">
      @foreach($groups as $g)
        <div class="card" style="padding:14px">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <strong>{{ $g->label ?? ('Groupe '.$g->id) }}</strong>
            <a class="btn" href="{{ route('admin.guards.show', $g) }}">Voir</a>
          </div>
          <form method="POST" action="{{ route('admin.guards.import', $g) }}" enctype="multipart/form-data" style="margin-top:10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            @csrf
            <input type="file" name="file" required
                   accept=".xlsx,.xls,.csv,.txt,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain">
            <button class="btn" type="submit">Importer</button>
          </form>
        </div>
      @endforeach
    </div>

  </div>
</div>
</body>
</html>
