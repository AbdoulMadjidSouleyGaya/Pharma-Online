<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pharmacies de Garde — {{ $group->label ?? ('Groupe '.$group->id) }}</title>
  <style>
    :root{--blue:#2b6cb0;--bg:#f3f6fb;--ink:#1f2937}
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink)}
    .wrap{max-width:1200px;margin:24px auto;padding:0 16px}
    .bar{display:flex;justify-content:space-between;align-items:center;margin:16px 0;gap:12px;flex-wrap:wrap}
    .btn{appearance:none;border:1px solid #ccd6e0;background:#fff;color:#111827;border-radius:8px;padding:8px 12px;text-decoration:none;font-weight:800;cursor:pointer}
    .btn:hover{background:#f1f5f9}
    .btn.green{background:#10b981;color:#fff;border-color:#0f9f75}
    .btn.green:hover{background:#0fa57b}
    .flash{margin:10px 0;padding:10px;border-radius:8px}
    .flash.ok{border:1px solid #c7eed8;background:#ecfdf5}
    .flash.err{border:1px solid #ffd2d2;background:#fff0f0}
    table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid #d0d7e2;border-radius:10px;overflow:hidden;background:#fff}
    th,td{padding:12px 14px;font-size:16px}
    thead th{background:var(--blue);color:#fff;text-align:left}
    thead th:first-child{width:70px;text-align:center}
    tbody tr:nth-child(even){background:#f7fafc}
    tbody td:first-child{text-align:center;font-weight:900}
    .muted{color:#6b7280}
    .file-input{padding:7px 10px;border:1px solid #ccd6e0;border-radius:8px;background:#fff}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="bar">
      <a class="btn" href="{{ route('admin.guards.index') }}">← Retour</a>

      <form method="POST" action="{{ route('admin.guards.import', $group) }}"
            enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        @csrf
        <input class="file-input" type="file" name="file" required
               accept=".xlsx,.xls,.csv,.txt,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain" />
        <button class="btn green" type="submit">Importer / Remplacer la liste</button>
      </form>
    </div>

    <h1 style="margin:0 0 6px;text-align:center;font-size:40px">
      {{ $group->label ?? ('Groupe '.$group->id) }}
    </h1>
    <p class="muted" style="text-align:center;margin-top:0">
      Liste fixe utilisée par la rotation automatique.
    </p>

    @if(session('status'))
      <div class="flash ok">{{ session('status') }}</div>
    @endif

    @if($errors->any())
      <div class="flash err">
        @foreach($errors->all() as $e)
          <div>• {{ $e }}</div>
        @endforeach
      </div>
    @endif

    <table>
      <thead>
        <tr>
          <th>N°</th>
          <th>Pharmacie</th>
          <th>Quartier</th>
          <th>Localisation</th>
          <th>Contact</th>
        </tr>
      </thead>
      <tbody>
        @php $i = 1; @endphp
        @forelse($pharmacies as $p)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->district ?? '—' }}</td>
            <td>{{ $p->address ?? '—' }}</td>
            <td>{{ $p->phone ?? '—' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="muted">Aucune pharmacie importée pour ce groupe.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

  </div>
</body>
</html>
