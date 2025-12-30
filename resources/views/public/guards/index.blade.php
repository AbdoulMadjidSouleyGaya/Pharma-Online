<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Pharmacies de garde — PharmaOnline</title>

  <style>
    :root{
      --bg:#f6f8fc;
      --panel:#ffffff;
      --panel2:#fbfdff;

      --ink:#0f172a;
      --muted:#64748b;
      --line:#e2e8f0;

      --brand:#2563eb;
      --brand2:#1d4ed8;
      --brandSoft:#eff6ff;

      --ok:#16a34a;
      --warn:#f59e0b;

      --radius:18px;
      --shadow:0 14px 40px rgba(2,6,23,.08);
      --shadow2:0 8px 20px rgba(2,6,23,.06);
    }

    *{box-sizing:border-box}
    html,body{margin:0;height:100%}
    body{
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--ink);
      background:
        radial-gradient(900px 500px at 15% 0%, rgba(37,99,235,.08), transparent 60%),
        radial-gradient(900px 500px at 90% 10%, rgba(56,189,248,.10), transparent 55%),
        linear-gradient(180deg, #ffffff, var(--bg));
    }

    a{color:inherit}
    .wrap{max-width:1220px;margin:0 auto;padding:22px 16px 34px}

    .topbar{
      position:sticky;top:0;z-index:40;
      background:rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      border-bottom:1px solid var(--line);
      box-shadow: 0 6px 16px rgba(2,6,23,.06);
    }
    .topin{
      max-width:1220px;margin:0 auto;padding:12px 16px;
      display:flex;align-items:center;justify-content:space-between;gap:12px
    }
    .brand{
      display:flex;align-items:center;gap:10px;
      font-weight:950;letter-spacing:.2px
    }
    .logo{
      width:38px;height:38px;border-radius:14px;
      display:grid;place-items:center;
      background:linear-gradient(135deg, rgba(37,99,235,.14), rgba(56,189,248,.10));
      border:1px solid rgba(37,99,235,.22);
      box-shadow:0 10px 25px rgba(2,6,23,.08);
      color:var(--brand2);
      font-weight:1000;
    }
    .brand small{
      display:block;margin-top:2px;
      font-size:12px;font-weight:800;color:var(--muted)
    }

    .nav{display:flex;gap:8px;flex-wrap:wrap}
    .nav a{
      text-decoration:none;
      padding:8px 10px;border-radius:12px;
      border:1px solid transparent;
      color:#0f172a;
      font-weight:850;font-size:14px
    }
    .nav a:hover{
      border-color:var(--line);
      background:rgba(2,6,23,.03)
    }

    .grid{
      margin-top:16px;
      display:grid;
      grid-template-columns: 1fr;
      gap:14px;
    }

    .card{
      background:linear-gradient(180deg, var(--panel), var(--panel2));
      border:1px solid var(--line);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      overflow:hidden;
    }

    .card-h{
      padding:16px 18px;
      border-bottom:1px solid var(--line);
      display:flex;align-items:center;justify-content:space-between;
      gap:12px;flex-wrap:wrap
    }
    .card-b{padding:16px 18px}

    .title{
      font-size:22px;font-weight:1000;margin:0;
      letter-spacing:.2px
    }
    .subtitle{
      margin:6px 0 0;color:var(--muted);font-weight:650;font-size:14px
    }

    .chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
    .chip{
      display:inline-flex;align-items:center;gap:8px;
      padding:8px 10px;border-radius:999px;
      border:1px solid var(--line);
      background:rgba(2,6,23,.02);
      font-weight:850;font-size:13px;color:#0f172a
    }
    .dot{width:8px;height:8px;border-radius:999px;background:var(--brand)}
    .dot.ok{background:var(--ok)}
    .dot.warn{background:var(--warn)}

    .btns{display:flex;gap:10px;flex-wrap:wrap}
    .btn{
      text-decoration:none;
      display:inline-flex;align-items:center;gap:10px;
      padding:10px 12px;border-radius:14px;
      border:1px solid var(--line);
      background:#fff;
      color:var(--ink);font-weight:950;font-size:14px;
      transition: transform .12s ease, filter .12s ease, box-shadow .12s ease;
      box-shadow: var(--shadow2);
    }
    .btn:hover{filter:brightness(.99); transform: translateY(-1px)}
    .btn.primary{
      background:linear-gradient(135deg, rgba(37,99,235,.12), rgba(37,99,235,.06));
      border-color: rgba(37,99,235,.30);
      color: var(--brand2);
    }
    .btn svg{width:16px;height:16px;opacity:.9}

    .table-wrap{
      border-radius:16px;
      border:1px solid var(--line);
      overflow:hidden;
      background:#fff;
    }
    table{width:100%;border-collapse:separate;border-spacing:0}
    thead th{
      text-align:left;
      padding:12px 12px;
      font-size:13px;
      text-transform:uppercase;
      letter-spacing:.08em;
      color:#fff;
      background:linear-gradient(90deg, var(--brand2), var(--brand));
      border-bottom:1px solid var(--line);
      white-space:nowrap;
    }
    tbody td{
      padding:12px 12px;
      border-bottom:1px solid var(--line);
      color:#0f172a;
      font-size:14px;
      vertical-align:top;
      background:#fff;
    }
    tbody tr:hover td{background:rgba(37,99,235,.04)}
    tbody tr:last-child td{border-bottom:none}

    .col-n{width:64px;text-align:center;font-weight:1000}
    .muted{color:var(--muted)}
    .nowrap{white-space:nowrap}

    .link-btn{
      display:inline-flex;align-items:center;gap:8px;
      padding:8px 10px;border-radius:12px;
      border:1px solid rgba(37,99,235,.25);
      background:var(--brandSoft);
      color:var(--brand2);
      font-weight:950;
      text-decoration:none;
      box-shadow: 0 6px 14px rgba(2,6,23,.06);
    }
    .link-btn:hover{filter:brightness(.98)}
    .link-btn svg{width:16px;height:16px}

    @media(max-width:820px){
      table, thead, tbody, th, td, tr{display:block}
      thead{display:none}
      tbody tr{
        padding:12px;
        border-bottom:1px solid var(--line);
        background:#fff;
      }
      tbody td{
        border:none;
        padding:6px 0;
        background:transparent;
      }
      .col-n{width:auto;text-align:left}
      .m-label{
        display:inline-block;
        min-width:120px;
        color:var(--muted);
        font-weight:900;
        margin-right:10px;
      }
    }

    .footer-note{
      margin-top:14px;
      color:var(--muted);
      font-size:13px;
      line-height:1.5;
      opacity:.98
    }

    .warnbox{
      padding:14px 16px;
      border:1px solid rgba(245,158,11,.35);
      background:rgba(245,158,11,.10);
      border-radius:16px;
      color:#7c2d12;
      font-weight:900;
      box-shadow: var(--shadow2);
    }
  </style>
</head>
<body>

<header class="topbar">
  <div class="topin">
    <div class="brand">
      <div class="logo">Rx</div>
      <div>
        Pharmacies de garde
        <small>Niamey • Lecture seule</small>
      </div>
    </div>

    <nav class="nav">
      <a href="{{ route('home') }}">Accueil</a>
      <a href="{{ route('contact') }}">Contact</a>
      @auth
        <a href="{{ route('dashboard') }}">Mon espace</a>
      @endauth
    </nav>
  </div>
</header>

<main class="wrap">

  @if(!($configured ?? false))
    <div class="warnbox">
      {{ $message ?? "Rotation non configurée." }}
    </div>
  @else
    @php
      \Carbon\Carbon::setLocale('fr');
      $ps = $period_start?->copy()->locale('fr')->isoFormat('dddd D MMMM YYYY [à] HH:mm');
      $pe = $period_end?->copy()->locale('fr')->isoFormat('dddd D MMMM YYYY [à] HH:mm');

      $title = $offset === 0 ? "Semaine en cours"
              : ($offset === -1 ? "Semaine passée"
              : ($offset === 1 ? "Semaine à venir"
              : "Semaine (".$offset.")"));

      $gLabel = $group?->label ?? ('Groupe '.($group?->id ?? '—'));
    @endphp

    <div class="grid">
      <section class="card">
        <div class="card-h">
          <div>
            <h1 class="title">{{ $gLabel }}</h1>
            <div class="subtitle">{{ $title }} — du <strong>{{ $ps }}</strong> au <strong>{{ $pe }}</strong></div>

            <div class="chips">
              <span class="chip"><span class="dot ok"></span> Rotation : samedi 13h → samedi 13h</span>
              <span class="chip"><span class="dot"></span> Région : <strong>{{ $region ?: '—' }}</strong></span>
              <span class="chip"><span class="dot warn"></span> Page publique (lecture seule)</span>
            </div>
          </div>

          <div class="btns">
            <a class="btn" href="{{ route('guards.public.page', ['offset' => $prevOffset]) }}">
              <svg viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Semaine passée
            </a>

            <a class="btn primary" href="{{ route('guards.public.page', ['offset' => 0]) }}">
              <svg viewBox="0 0 24 24" fill="none"><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/></svg>
              Aujourd’hui
            </a>

            <a class="btn" href="{{ route('guards.public.page', ['offset' => $nextOffset]) }}">
              Semaine à venir
              <svg viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          </div>
        </div>

        <div class="card-b">
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th class="col-n">N°</th>
                  <th>Pharmacie</th>
                  <th>Quartier</th>
                  <th>Localisation</th>
                  <th class="nowrap">Itinéraire</th>
                  <th class="nowrap">Contact</th>
                </tr>
              </thead>

              <tbody>
                @php $i = 1; @endphp

                @forelse($pharmacies as $p)
                  @php
                    // ✅ 1) Si tu as des colonnes latitude/longitude : itinéraire EXACT
                    $hasGps = !empty($p->latitude) && !empty($p->longitude);

                    // ✅ 2) Sinon : fallback recherche (approx)
                    $query = trim(($p->name ?? '').' '.($p->district ?? '').' Niamey Niger');

                    $url = $hasGps
                      ? 'https://www.google.com/maps/dir/?api=1&destination=' . $p->latitude . ',' . $p->longitude
                      : ($query !== '' ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($query) : null);
                  @endphp

                  <tr>
                    <td class="col-n">{{ $i++ }}</td>

                    <td>
                      <div style="font-weight:1000">{{ $p->name }}</div>
                      <div class="muted" style="font-size:12px;margin-top:2px">{{ $gLabel }}</div>
                    </td>

                    <td>
                      <span class="m-label">Quartier</span>
                      {{ $p->district ?? '—' }}
                    </td>

                    <td>
                      <span class="m-label">Localisation</span>
                      {{ $p->address ?? '—' }}
                    </td>

                    <td class="nowrap">
                      <span class="m-label">Itinéraire</span>
                      @if($url)
                        <a class="link-btn" href="{{ $url }}" target="_blank" rel="noopener">
                          <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 10.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" stroke="currentColor" stroke-width="2"/>
                          </svg>
                          Ouvrir
                        </a>
                      @else
                        <span class="muted">—</span>
                      @endif
                    </td>

                    <td class="nowrap">
                      <span class="m-label">Contact</span>
                      {{ $p->phone ?? '—' }}
                    </td>
                  </tr>

                @empty
                  <tr>
                    <td colspan="6" class="muted" style="padding:14px">
                      Aucune pharmacie importée pour ce groupe.
                    </td>
                  </tr>
                @endforelse

              </tbody>
            </table>
          </div>

          <div class="footer-note">
            Les données affichées ici proviennent des groupes importés par l’administrateur.
            Cette page est strictement en lecture seule.
          </div>
        </div>
      </section>
    </div>
  @endif
</main>

</body>
</html>
