<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Gérer les Pharmaciens</title>

  <style>
    :root{
      --bg:#020617;
      --bg-soft:#020617;
      --surface:#020617;
      --panel:#020617;
      --card:#020617;
      --border:#1f2937;
      --border-soft:#1e293b;
      --accent:#22c55e;
      --accent-soft:#bbf7d0;
      --accent-strong:#16a34a;
      --danger:#ef4444;
      --danger-soft:#fee2e2;
      --ink:#e5e7eb;
      --muted:#9ca3af;
      --chip:#020617;
      --blue:#3b82f6;
      --indigo:#6366f1;
    }

    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:
        radial-gradient(circle at top left, rgba(34,197,94,.25), transparent 60%),
        radial-gradient(circle at bottom right, rgba(56,189,248,.18), transparent 60%),
        #020617;
      color:var(--ink);
      min-height:100vh;
    }
    .page{
      max-width:1120px;
      margin:28px auto 40px;
      padding:0 18px;
    }

    /* ===== HEADER ===== */
    .header-shell{
      background:radial-gradient(circle at top left, rgba(34,197,94,.35), transparent 60%),
                  radial-gradient(circle at bottom right, rgba(56,189,248,.25), transparent 60%),
                  linear-gradient(135deg, #020617, #020617);
      border-radius:22px;
      border:1px solid rgba(148,163,184,.4);
      padding:18px 18px 18px;
      box-shadow:
        0 18px 60px rgba(15,23,42,.8),
        0 0 0 1px rgba(15,23,42,.9);
      position:relative;
      overflow:hidden;
    }
    .header-shell::before{
      content:"";
      position:absolute;
      inset:-40%;
      background:
        radial-gradient(circle at top, rgba(34,197,94,.16), transparent 65%),
        radial-gradient(circle at right, rgba(59,130,246,.12), transparent 70%);
      opacity:1;
      pointer-events:none;
    }
    .header-inner{
      position:relative;
      display:flex;
      flex-wrap:wrap;
      justify-content:space-between;
      gap:16px;
      align-items:flex-start;
    }
    h1{
      margin:0 0 4px;
      font-size:24px;
      font-weight:800;
      letter-spacing:.03em;
      text-transform:uppercase;
      color:#f9fafb;
    }
    .headline-sub{
      margin:0;
      font-size:13px;
      color:var(--muted);
    }
    .pill-row{
      display:flex;
      flex-wrap:wrap;
      gap:6px;
      margin-top:10px;
    }
    .pill{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:4px 9px;
      border-radius:999px;
      font-size:11px;
      background:rgba(15,23,42,.9);
      color:var(--muted);
      border:1px solid rgba(148,163,184,.45);
    }
    .pill strong{
      color:#e5e7eb;
      font-weight:600;
    }

    .header-actions{
      display:flex;
      flex-direction:column;
      align-items:flex-end;
      gap:8px;
    }
    .btn{
      appearance:none;
      border-radius:999px;
      padding:7px 14px;
      font-size:13px;
      font-weight:600;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      gap:7px;
      border:1px solid rgba(148,163,184,.6);
      background:rgba(15,23,42,.92);
      color:#e5e7eb;
      text-decoration:none;
      transition:background .18s ease, transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .btn:hover{
      background:rgba(15,23,42,1);
      transform:translateY(-1px);
      box-shadow:0 15px 35px rgba(15,23,42,.7);
    }
    .btn span.icon{
      font-size:15px;
    }
    .btn.primary{
      background:linear-gradient(135deg, var(--accent), var(--accent-strong));
      border-color:rgba(34,197,94,.7);
      color:#022c22;
      box-shadow:
        0 0 0 1px rgba(22,163,74,.4),
        0 12px 35px rgba(22,163,74,.7);
    }
    .btn.primary:hover{
      transform:translateY(-1px);
      box-shadow:
        0 0 0 1px rgba(22,163,74,.55),
        0 16px 40px rgba(22,163,74,.9);
      filter:brightness(1.04);
    }
    .tag-small{
      font-size:11px;
      color:var(--muted);
    }

    /* ===== FLASH / ERRORS ===== */
    .flash{
      margin-top:14px;
      border-radius:12px;
      padding:9px 11px;
      font-size:13px;
      display:flex;
      align-items:flex-start;
      gap:8px;
    }
    .flash.ok{
      background:rgba(5,46,22,.9);
      border:1px solid rgba(34,197,94,.6);
      color:#bbf7d0;
    }
    .flash.err{
      background:rgba(127,29,29,.9);
      border:1px solid rgba(248,113,113,.7);
      color:#fee2e2;
    }

    /* ===== TOP BAR (search + stats) ===== */
    .top-bar{
      margin-top:18px;
      display:flex;
      flex-wrap:wrap;
      gap:14px;
      justify-content:space-between;
      align-items:flex-end;
    }
    .search-box{
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      align-items:center;
    }
    .input{
      padding:8px 10px;
      border-radius:999px;
      border:1px solid rgba(148,163,184,.7);
      background:rgba(15,23,42,.96);
      color:#e5e7eb;
      font-size:13px;
      min-width:230px;
      outline:none;
      transition:border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }
    .input::placeholder{
      color:#6b7280;
    }
    .input:focus{
      border-color:rgba(59,130,246,.7);
      box-shadow:0 0 0 1px rgba(59,130,246,.5);
      background:rgba(15,23,42,1);
    }
    .btn-ghost{
      appearance:none;
      border-radius:999px;
      padding:7px 11px;
      font-size:13px;
      font-weight:600;
      cursor:pointer;
      border:1px solid rgba(148,163,184,.6);
      background:rgba(15,23,42,.9);
      color:#e5e7eb;
      display:inline-flex;
      align-items:center;
      gap:6px;
      transition:background .18s ease, border-color .18s ease, transform .18s ease;
    }
    .btn-ghost:hover{
      background:rgba(15,23,42,1);
      border-color:rgba(148,163,184,.9);
      transform:translateY(-1px);
    }

    .mini-stats{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
    }
    .mini-pill{
      border-radius:999px;
      border:1px solid rgba(148,163,184,.45);
      padding:6px 10px;
      font-size:11px;
      color:var(--muted);
      display:inline-flex;
      align-items:center;
      gap:6px;
      background:rgba(15,23,42,.9);
    }
    .mini-pill strong{
      color:#e5e7eb;
      font-weight:600;
    }

    /* ===== TABLE ===== */
    .table-shell{
      margin-top:18px;
      border-radius:20px;
      background:rgba(15,23,42,.97);
      border:1px solid rgba(30,41,59,.9);
      box-shadow:
        0 12px 35px rgba(15,23,42,.9),
        0 0 0 1px rgba(15,23,42,1);
      overflow:hidden;
    }
    table{
      width:100%;
      border-collapse:separate;
      border-spacing:0;
    }
    thead th{
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:.08em;
      text-align:left;
      padding:10px 14px;
      background:radial-gradient(circle at top left, rgba(248,250,252,.22), transparent 65%),
                 rgba(15,23,42,.98);
      color:#9ca3af;
      border-bottom:1px solid rgba(30,64,175,.85);
    }
    tbody td{
      padding:10px 14px;
      font-size:13px;
      border-bottom:1px solid rgba(30,41,59,.9);
      color:#e5e7eb;
    }
    tbody tr:nth-child(even){
      background:rgba(15,23,42,.96);
    }
    tbody tr:nth-child(odd){
      background:rgba(15,23,42,.98);
    }
    tbody tr:last-child td{
      border-bottom:none;
    }
    .muted{
      color:var(--muted);
      font-size:13px;
    }
    .col-name{
      font-weight:600;
    }
    .col-email{
      font-size:12px;
      color:var(--muted);
    }
    .col-date{
      white-space:nowrap;
      font-size:12px;
      color:var(--muted);
    }

    .badge-pharmacy{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:3px 9px;
      border-radius:999px;
      background:rgba(15,23,42,.95);
      border:1px solid rgba(148,163,184,.55);
      font-size:11px;
      color:#e5e7eb;
    }
    .badge-pharmacy span.icon{
      font-size:13px;
      opacity:.8;
    }

    .col-actions{
      width:220px;
    }
    .actions-row{
      display:flex;
      flex-wrap:wrap;
      gap:6px;
    }
    .btn-table{
      appearance:none;
      border-radius:999px;
      padding:5px 10px;
      font-size:11px;
      font-weight:600;
      cursor:pointer;
      display:inline-flex;
      align-items:center;
      gap:5px;
      border:1px solid rgba(148,163,184,.7);
      background:rgba(15,23,42,.95);
      color:#e5e7eb;
      text-decoration:none;
      transition:background .16s ease, border-color .16s ease, transform .16s ease;
    }
    .btn-table:hover{
      background:rgba(15,23,42,1);
      border-color:rgba(148,163,184,.95);
      transform:translateY(-1px);
    }
    .btn-table.blue{
      border-color:rgba(59,130,246,.8);
      background:rgba(15,23,42,.98);
      color:#dbeafe;
    }
    .btn-table.indigo{
      border-color:rgba(129,140,248,.85);
      background:rgba(17,24,39,.98);
      color:#e0e7ff;
    }
    .btn-table.red{
      border-color:rgba(248,113,113,.85);
      background:rgba(69,10,10,.96);
      color:#fee2e2;
    }

    /* ===== PAGINATION WRAPPER ===== */
    .pagination-wrap{
      margin-top:14px;
      display:flex;
      justify-content:flex-end;
      font-size:13px;
    }

  </style>
</head>
<body>
  <div class="page">

    {{-- HEADER --}}
    <div class="header-shell">
      <div class="header-inner">
        <div>
          <h1>Pharmaciens</h1>
          <p class="headline-sub">
            Gestion centralisée des comptes pharmaciens liés aux pharmacies partenaires.
          </p>
          <div class="pill-row">
            <div class="pill">
              🧑‍⚕️ <strong>Comptes Pharmaciens</strong>
              <span style="opacity:.8;">
                {{ $pharmacists->total() }} au total
              </span>
            </div>
            <div class="pill">
              🔐 Accès API AbdouPharma gérés côté administrateur
            </div>
          </div>
        </div>

        <div class="header-actions">
          <a href="{{ route('admin.dashboard') }}" class="btn">
            <span class="icon">←</span>
            <span>Retour au tableau de bord</span>
          </a>
          <a href="{{ route('admin.pharmacists.create') }}" class="btn primary">
            <span class="icon">➕</span>
            <span>Créer un compte Pharmacien</span>
          </a>
          <div class="tag-small">
            Une pharmacie → un compte pharmacien principal.
          </div>
        </div>
      </div>
    </div>

    {{-- FLASH / ERREURS --}}
    @if(session('status'))
      <div class="flash ok">
        <span>✅</span>
        <div>{{ session('status') }}</div>
      </div>
    @endif

    @if($errors->any())
      <div class="flash err">
        <span>⚠️</span>
        <div>
          @foreach($errors->all() as $e)
            <div>{{ $e }}</div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- BARRE DU HAUT : RECHERCHE + MINI STATS --}}
    <div class="top-bar">
      <form method="GET" action="{{ route('admin.pharmacists.index') }}" class="search-box">
        <input class="input"
               type="text"
               name="q"
               value="{{ $q }}"
               placeholder="Recherche (nom, email, pharmacie…)"/>
        <button class="btn-ghost" type="submit">
          🔍 Rechercher
        </button>
      </form>

      <div class="mini-stats">
        <div class="mini-pill">
          📧 <span>Comptes actifs : <strong>{{ $pharmacists->total() }}</strong></span>
        </div>
        @if($q)
          <div class="mini-pill">
            🔎 <span>Filtre appliqué : <strong>{{ $q }}</strong></span>
          </div>
        @endif
      </div>
    </div>

    {{-- TABLE --}}
    <div class="table-shell">
      <table>
        <thead>
          <tr>
            <th>Pharmacien</th>
            <th>Pharmacie</th>
            <th>Email</th>
            <th>Créé le</th>
            <th class="col-actions">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($pharmacists as $u)
          <tr>
            <td>
              <div class="col-name">{{ $u->name }}</div>
            </td>
            <td>
              @php
                $pharmacy = optional($u->pharmacy);
              @endphp
              @if($pharmacy)
                <span class="badge-pharmacy">
                  <span class="icon">🏥</span>
                  <span>{{ $pharmacy->name }}</span>
                </span>
              @else
                <span class="muted">— Aucune pharmacie associée</span>
              @endif
            </td>
            <td>
              <div class="col-email">{{ $u->email }}</div>
            </td>
            <td>
              <div class="col-date">
                {{ optional($u->created_at)->format('Y-m-d H:i') }}
              </div>
            </td>
            <td class="col-actions">
              <div class="actions-row">
                {{-- Modifier --}}
                <a href="{{ route('admin.pharmacists.edit', $u) }}" class="btn-table blue">
                  ✏️ Modifier
                </a>

                {{-- Token --}}
                <a href="{{ route('admin.pharmacists.token', $u) }}" class="btn-table indigo">
                  🔑 Token
                </a>

                {{-- Supprimer --}}
                <form method="POST"
                      action="{{ route('admin.pharmacists.destroy', $u) }}"
                      onsubmit="return confirm('Supprimer ce pharmacien ? Cette action est irréversible.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-table red">
                    🗑 Supprimer
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="muted" style="padding:14px;">
              Aucun pharmacien enregistré pour le moment.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    {{-- PAGINATION --}}
    <div class="pagination-wrap">
      {{ $pharmacists->links() }}
    </div>
  </div>
</body>
</html>
