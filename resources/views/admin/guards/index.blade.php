<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — Pharmacies de garde</title>
<style>
  :root{--bg:#0b1020;--ink:#fff;--muted:#a8b1c7;--stroke:#1f2937;--shadow:0 30px 60px rgba(0,0,0,.35)}
  *{box-sizing:border-box} body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:linear-gradient(180deg,#0b1020,#0a0f1f 60%,#0b0f1e);color:var(--ink)}
  .top{position:sticky;top:0;display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:linear-gradient(180deg,#0e1529,#0b1222);border-bottom:1px solid #0b1222;box-shadow:0 10px 30px rgba(0,0,0,.25)}
  .btn{appearance:none;border:1px solid #1f2937;background:#111827;color:#fff;border-radius:10px;padding:8px 12px;text-decoration:none;font-weight:800}
  .btn:hover{background:#0f172a}
  .btn.green{background:#10b981;border-color:#128a66}.btn.green:hover{background:#0fa57b}
  .wrap{max-width:1200px;margin:22px auto;padding:0 18px}
  .panel{background:linear-gradient(180deg,#0d1528,#0a1120);border:1px solid var(--stroke);border-radius:16px;padding:16px;box-shadow:var(--shadow)}
  .grid{display:grid;grid-template-columns:1.3fr .7fr;gap:14px}
  @media(max-width:980px){.grid{grid-template-columns:1fr}}
  .muted{color:var(--muted)}
  table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid #1f2937;border-radius:14px;overflow:hidden;background:#0b1222}
  th,td{padding:10px 12px;text-align:left;font-size:14px}
  thead th{background:#0d1528;color:#c7d2fe;border-bottom:1px solid #1f2937}
  tbody tr:nth-child(even){background:#0a111f}
  .ok{background:#0b2a1f;border:1px solid #14532d;color:#d1fae5;padding:10px;border-radius:10px;margin-bottom:12px}
</style>
</head>
<body>
  <div class="top">
    <a class="btn" href="{{ route('admin.dashboard') }}">← Tableau de bord</a>
    <a class="btn green" href="{{ route('admin.guards.create') }}">⚙️ Config / Import groupes</a>
  </div>

  <div class="wrap">
    @if(session('status')) <div class="ok">{{ session('status') }}</div> @endif

    <div class="grid">
      <div class="panel">
        <h2 style="margin:0 0 6px">Groupe actuellement de garde</h2>

        @if(!($state['configured'] ?? false))
          <div class="muted">
            Rotation non configurée. Clique sur <strong>Config / Import groupes</strong>.
          </div>
        @else
          @php
            \Carbon\Carbon::setLocale('fr');
            $cs = $state['current_start_at']->copy()->locale('fr')->isoFormat('dddd D MMMM YYYY [à] HH:mm');
            $ce = $state['current_end_at']->copy()->locale('fr')->isoFormat('dddd D MMMM YYYY [à] HH:mm');
          @endphp

          <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
            <div>
              <div style="font-size:26px;font-weight:900">Groupe {{ $state['current_group'] }}</div>
              <div class="muted">Du {{ $cs }} au {{ $ce }}</div>
              <div class="muted">Région : {{ $state['config']->region ?: '—' }}</div>
            </div>
            @if($activeGroup)
              <a class="btn" href="{{ route('admin.guards.show', $activeGroup) }}">Voir ce groupe</a>
            @endif
          </div>

          <div style="margin-top:14px">
            <table>
              <thead>
                <tr>
                  <th style="width:60px;text-align:center">N°</th>
                  <th>Pharmacie</th>
                  <th>Quartier</th>
                  <th>Localisation</th>
                  <th>Contact</th>
                </tr>
              </thead>
              <tbody>
                @php $i=1; @endphp
                @forelse($activePharmacies as $p)
                  <tr>
                    <td style="text-align:center;font-weight:800">{{ $i++ }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->district ?? '—' }}</td>
                    <td>{{ $p->address ?? '—' }}</td>
                    <td>{{ $p->phone ?? '—' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="muted">Aucune pharmacie importée pour ce groupe.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        @endif
      </div>

      <div class="panel">
        <h3 style="margin:0 0 10px">Aperçu prochaines semaines</h3>

        @if(($state['configured'] ?? false))
          <table>
            <thead>
              <tr>
                <th>Groupe</th>
                <th>Période</th>
              </tr>
            </thead>
            <tbody>
              @foreach($preview as $p)
                @php
                  $s = $p['start']->copy()->locale('fr')->isoFormat('D MMM');
                  $e = $p['end']->copy()->locale('fr')->isoFormat('D MMM');
                @endphp
                <tr>
                  <td style="font-weight:900">G{{ $p['group'] }}</td>
                  <td class="muted">{{ $s }} → {{ $e }} (13h)</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div class="muted">Configure d’abord la rotation.</div>
        @endif

        <div style="margin-top:12px">
          <h4 style="margin:0 0 6px">Groupes</h4>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            @foreach($groups as $g)
              <a class="btn" href="{{ route('admin.guards.show', $g) }}">{{ $g->label ?? ('Groupe '.$g->id) }}</a>
            @endforeach
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
