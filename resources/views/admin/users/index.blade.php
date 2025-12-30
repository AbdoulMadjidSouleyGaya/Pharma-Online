<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PharmaOnline — Admin · Utilisateurs</title>
  <style>
    :root{
      --bg:#0b1020; --surface:#0f172a; --ink:#fff; --muted:#a8b1c7;
      --stroke:#1f2937; --stroke2:#0b1222; --shadow:0 30px 60px rgba(0,0,0,.35);
      --ok:#ecfdf5; --ok-b:#a7f3d0; --ok-t:#065f46;
      --err:#fff0f0; --err-b:#ffd2d2; --err-t:#7a1111;
    }
    *{box-sizing:border-box} html,body{height:100%}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:linear-gradient(180deg,#0b1020,#0a0f1f 60%,#0b0f1e);color:var(--ink)}
    .top{position:sticky;top:0;z-index:40;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px;border-bottom:1px solid var(--stroke2);background:linear-gradient(180deg,#0e1529,#0b1222);box-shadow:0 10px 30px rgba(0,0,0,.25)}
    .brand{display:flex;align-items:center;gap:10px}
    .brand img{width:36px;height:36px;object-fit:contain}
    .brand .t b{display:block;font-size:16px}
    .brand .t span{font-size:12px;color:var(--muted)}
    .right{display:flex;gap:8px;align-items:center}
    .btn{appearance:none;border:1px solid #1f2937;background:#111827;color:#fff;border-radius:10px;padding:8px 12px;text-decoration:none;font-weight:700}
    .btn:hover{background:#0f172a}
    .wrap{max-width:1100px;margin:22px auto;padding:0 18px}
    .panel{background:linear-gradient(180deg,#0d1528,#0a1120);border:1px solid var(--stroke);border-radius:16px;padding:16px;box-shadow:var(--shadow)}
    .title{margin:0 0 8px}
    .muted{color:var(--muted);font-size:13px;margin:0 0 12px}
    table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid var(--stroke);border-radius:14px;overflow:hidden}
    th,td{padding:10px 12px;text-align:left;font-size:14px}
    thead th{background:#0d1528;color:#c7d2fe;border-bottom:1px solid var(--stroke)}
    tbody tr{background:#0b1222;border-bottom:1px solid var(--stroke)}
    tbody tr:nth-child(even){background:#0a111f}
    .alert{padding:10px 12px;border-radius:10px;margin-bottom:12px;font-size:14px}
    .ok{background:var(--ok);border:1px solid var(--ok-b);color:var(--ok-t)}
    .err{background:var(--err);border:1px solid var(--err-b);color:var(--err-t)}
    .delbtn{appearance:none;background:#7f1d1d;color:#fff;border:1px solid #991b1b;padding:8px 10px;border-radius:8px;cursor:pointer;font-weight:700}
    .delbtn:hover{background:#991b1b}
    .delbtn[disabled]{opacity:.55;cursor:not-allowed}
    .bar{display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:10px}
    .search{display:flex;gap:8px}
    .search input{padding:8px 10px;border-radius:8px;border:1px solid #334155;background:#0b1222;color:#e5e7eb}
  </style>
</head>

<body>
  <div class="top">
    <div class="brand">
      <img src="{{ asset('images/logo.png') }}" alt="PharmaOnline">
      <div class="t"><b>PharmaOnline</b><span>Administration — Utilisateurs</span></div>
    </div>
    <div class="right">
      <a class="btn" href="{{ route('admin.dashboard') }}">← Tableau de bord</a>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn" type="submit">Se déconnecter</button>
      </form>
    </div>
  </div>

  <div class="wrap">
    <div class="panel">
      <h2 class="title">Liste des utilisateurs</h2>
      <p class="muted">Tous les comptes inscrits (suppression possible, sauf administrateurs et votre propre compte).</p>

      @if (session('status'))
        <div class="alert ok">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert err">
          @foreach ($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
      @endif

        <div class="bar">
  <div class="muted">
    Total : {{ number_format($users->total(), 0, ',', ' ') }}
    @if(!empty($q)) — filtre « {{ $q }} » @endif
  </div>

  <div style="display:flex; gap:8px; align-items:center">
    {{-- Export CSV (préserve le filtre q si présent) --}}
    <a class="btn" href="{{ route('admin.users.export', array_filter(['q' => $q ?? null])) }}">
      ⬇️ Export CSV
    </a>

    {{-- Recherche --}}
    <form method="GET" class="search">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Rechercher nom ou email…">
      <button class="btn" type="submit">Rechercher</button>
    </form>
  </div>
</div>


      <table aria-label="Utilisateurs">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Créé le</th>
            <th style="width:140px">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            @php
              $isAdmin = method_exists($u,'hasRole') ? $u->hasRole('admin') : false;
              $isMe = auth()->id() === $u->id;
            @endphp
            <tr>
              <td>{{ $u->name }}</td>
              <td>{{ $u->email }}</td>
              <td>{{ $u->created_at?->format('d/m/Y H:i') }}</td>
              <td>
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                      onsubmit="return confirm('Supprimer définitivement le compte de {{ $u->name }} ?');">
                  @csrf
                  @method('DELETE')
                  <button class="delbtn" type="submit" {{ ($isAdmin || $isMe) ? 'disabled' : '' }}>
                    Supprimer
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="muted">Aucun utilisateur pour le moment.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div style="margin-top:10px">
        {{ $users->links() }}
      </div>
    </div>
  </div>
</body>
</html>
