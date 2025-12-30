<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Pharmacies</title>
  <style>
    :root{--bg:#0b1020;--ink:#fff;--muted:#a8b1c7;--stroke:#1f2937;--stroke2:#0b1222;--shadow:0 30px 60px rgba(0,0,0,.35)}
    *{box-sizing:border-box} html,body{height:100%}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:linear-gradient(180deg,#0b1020,#0a0f1f 60%,#0b0f1e);color:var(--ink)}
    .top{position:sticky;top:0;z-index:40;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px;border-bottom:1px solid var(--stroke2);background:linear-gradient(180deg,#0e1529,#0b1222);box-shadow:0 10px 30px rgba(0,0,0,.25)}
    .brand{display:flex;align-items:center;gap:10px}
    .brand img{width:36px;height:36px;object-fit:contain}
    .brand .t b{display:block;font-size:16px}.brand .t span{font-size:12px;color:var(--muted)}
    .right{display:flex;gap:8px;align-items:center}
    .btn{appearance:none;border:1px solid #1f2937;background:#111827;color:#fff;border-radius:10px;padding:8px 12px;text-decoration:none;font-weight:700}
    .btn:hover{background:#0f172a}
    .btn.green{background:#10b981;border-color:#128a66}.btn.green:hover{background:#0fa57b}
    .wrap{max-width:1100px;margin:22px auto;padding:0 18px}
    .panel{background:linear-gradient(180deg,#0d1528,#0a1120);border:1px solid var(--stroke);border-radius:16px;padding:16px;box-shadow:var(--shadow)}
    .title{margin:0 0 8px}.muted{color:var(--muted);font-size:13px;margin:0 0 12px}
    .bar{display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:10px}
    .search{display:flex;gap:8px}.search input{padding:8px 10px;border-radius:8px;border:1px solid #334155;background:#0b1222;color:#e5e7eb}
    table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid #1f2937;border-radius:14px;overflow:hidden}
    th,td{padding:10px 12px;text-align:left;font-size:14px}
    thead th{background:#0d1528;color:#c7d2fe;border-bottom:1px solid var(--stroke)}
    tbody tr{background:#0b1222;border-bottom:1px solid var(--stroke)}
    tbody tr:nth-child(even){background:#0a111f}
    .ok{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:10px 12px;border-radius:10px;margin-bottom:12px}
    .err{background:#fff0f0;border:1px solid #ffd2d2;color:#7a1111;padding:10px 12px;border-radius:10px;margin-bottom:12px}
    .del{appearance:none;background:#7f1d1d;color:#fff;border:1px solid #991b1b;padding:8px 10px;border-radius:8px;cursor:pointer;font-weight:700}
    .del:hover{background:#991b1b}
  </style>
</head>
<body>
  <div class="top">
    <div class="brand">
      <img src="{{ asset('images/logo.png') }}" alt="PharmaOnline">
      <div class="t"><b>PharmaOnline</b><span>Administration — Pharmacies</span></div>
    </div>
    <div class="right">
      <a class="btn" href="{{ route('admin.dashboard') }}">← Tableau de bord</a>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf <button class="btn" type="submit">Se déconnecter</button>
      </form>
    </div>
  </div>

  <div class="wrap">
    <div class="panel">
      <h2 class="title">Liste des pharmacies</h2>
      <p class="muted">Ajoutez, modifiez ou supprimez les pharmacies. Recherche par nom, quartier ou contact.</p>

      @if(session('status')) <div class="ok">{{ session('status') }}</div> @endif
      @if($errors->any()) <div class="err">@foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach</div> @endif

      <div class="bar">
  <a class="btn green" href="{{ route('admin.pharmacies.create') }}">+ Ajouter une pharmacie</a>

  <div style="display:flex; gap:8px; align-items:center">
    <a class="btn" href="{{ route('admin.pharmacies.export', array_filter(['q' => $q ?? null])) }}">
      ⬇️ Export CSV
    </a>

    <form method="GET" class="search">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Rechercher (nom, quartier, contact)…">
      <button class="btn" type="submit">Rechercher</button>
    </form>
  </div>
</div>


      <table aria-label="Pharmacies">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Quartier</th>
            <th>Localisation</th>
            <th>Contact</th>
            <th>Ajoutée le</th>
            <th style="width:160px">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pharmacies as $p)
            <tr>
              <td><strong>{{ $p->name }}</strong></td>
              <td>{{ $p->district }}</td>
              <td>
                @if($p->address)
                  {{ $p->address }}
                @else
                  <span class="muted">—</span>
                @endif
              </td>
              <td>
                @if($p->phone) <div>{{ $p->phone }}</div> @endif
                @if($p->email) <div><small class="muted">{{ $p->email }}</small></div> @endif
              </td>
              <td>{{ $p->created_at?->format('d/m/Y H:i') }}</td>
              <td>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                  <a class="btn" href="{{ route('admin.pharmacies.edit', $p) }}">Modifier</a>
                  <form method="POST" action="{{ route('admin.pharmacies.destroy', $p) }}"
                        onsubmit="return confirm('Supprimer définitivement {{ $p->name }} ?');">
                    @csrf @method('DELETE')
                    <button class="del" type="submit">Supprimer</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="muted">Aucune pharmacie enregistrée pour le moment.</td></tr>
          @endforelse
        </tbody>
      </table>

      <div style="margin-top:10px">{{ $pharmacies->links() }}</div>
    </div>
  </div>
</body>
</html>
