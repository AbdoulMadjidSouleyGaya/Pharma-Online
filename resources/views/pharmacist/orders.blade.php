<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Commandes — Pharmacien</title>
  <style>
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f6f7fb;color:#0f172a}
    .wrap{max-width:1000px;margin:24px auto;padding:0 16px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px}
    .muted{color:#6b7280}
    .pill{display:inline-block;padding:4px 10px;border-radius:999px;background:#eef2ff;color:#1e40af;font-weight:700;font-size:12px}
    .row{display:flex;justify-content:space-between;align-items:center}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="row" style="margin-bottom:12px">
      <h1 style="margin:0">Commandes</h1>
      @php $s = request('status','pending'); @endphp
      <span class="pill">Statut : {{ strtoupper($s) }}</span>
    </div>
    <div class="card">
      <p class="muted">Page placeholder. Ici on affichera la liste des commandes filtrées par le statut sélectionné.</p>
      <ul>
        <li>À venir : tableau des commandes (N°, client, montant, date, statut).</li>
        <li>Filtre par statut = <code>?status=pending|validated|rejected</code>.</li>
        <li>Actions : voir, valider, rejeter, exporter CSV, etc.</li>
      </ul>
    </div>
  </div>
</body>
</html>
