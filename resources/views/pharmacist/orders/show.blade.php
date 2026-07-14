<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Commande #{{ $order->number ?? '—' }} — Détail</title>
  <style>
    :root{
      --bg:#0b1220; --ink:#eef4ff; --muted:#a7b5cf; --line:#ffffff22;
      --panel:#0f1b3a; --panel2:#132650; --accent:#38bdf8;
      --ok:#10b981; --ko:#ef4444; --warn:#f59e0b; --brand:#2563eb;
      --radius:16px; --shadow:0 14px 40px rgba(0,0,0,.28);
    }
    *{box-sizing:border-box}
    html,body{
      margin:0;background:
        radial-gradient(1400px 700px at 60% -20%, #1e3a8a33, transparent 55%),
        linear-gradient(160deg,#0b1020 0%, #0b1220 60%, #0a1528 100%);
      color:var(--ink);font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif
    }
    a{text-decoration:none;color:inherit}
    .wrap{max-width:1200px;margin:28px auto;padding:0 18px}

    /* Cards */
    .card{background:linear-gradient(180deg,var(--panel),var(--panel2));border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);padding:18px}
    .head{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap}
    .title{margin:0;font-size:26px;font-weight:900;letter-spacing:.2px}
    .sub{color:var(--muted);margin-top:6px;line-height:1.4}
    .tag{display:inline-flex;gap:8px;align-items:center;border:1px solid var(--line);background:#ffffff14;padding:6px 10px;border-radius:999px;font-weight:800;font-size:12px}
    .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;border:1px solid var(--line);font-size:12px;font-weight:800;background:#ffffff12}
    .status-ok{background:#0ec29a33;border-color:#0ec29a55}
    .status-wait{background:#f59e0b33;border-color:#f59e0b66}
    .status-ko{background:#ef444433;border-color:#ef444466}

    /* Layout */
    .grid{display:grid;grid-template-columns:2fr 420px;gap:18px;margin-top:18px}
    @media(max-width:1080px){.grid{grid-template-columns:1fr}}
    .section h2{margin:0 0 12px;font-size:16px;letter-spacing:.2px}

    /* Table produits */
    .table{width:100%;border-collapse:separate;border-spacing:0}
    .table th{font-size:12px;color:#9fb4d4;text-transform:uppercase;letter-spacing:.3px;text-align:left;padding:10px;border-bottom:1px solid var(--line)}
    .table td{padding:10px;border-bottom:1px solid var(--line)}
    .table tfoot td{font-weight:900}
    .muted{color:var(--muted)}
    .hr{height:1px;background:var(--line);margin:14px 0}

    /* KPIs top */
    .kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:14px}
    @media(max-width:720px){.kpis{grid-template-columns:1fr 1fr}}
    .kpi{background:#0d1a36;border:1px solid #2b3b63;border-radius:12px;padding:12px 14px}
    .kpi .label{font-size:12px;color:#a9b7d3;text-transform:uppercase;letter-spacing:.3px}
    .kpi .value{margin-top:4px;font-weight:900;font-size:18px}

    /* Action panel (sticky) */
    .aside{position:sticky;top:14px;display:flex;flex-direction:column;gap:14px}
    .stack{display:flex;flex-direction:column;gap:14px}
    .form{display:flex;flex-direction:column;gap:12px}
    .btn{appearance:none;border:1px solid #2b3b63;background:#0e1737;color:#e5e7eb;border-radius:10px;padding:12px 16px;font-weight:900;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:transform .12s ease, background .18s ease, border-color .18s ease}
    .btn:hover{transform:translateY(-2px);background:#152558;border-color:#3b82f6}
    .btn.ok{background:linear-gradient(135deg,#0ec29a,#10b981);border-color:#10b981;color:#0b1220}
    .btn.ko{background:linear-gradient(135deg,#ef4444,#f97316);border-color:#ef4444;color:#fff}
    .btn.outline{background:transparent}
    .disabled{opacity:.6;pointer-events:none}

    /* Inputs */
    textarea{
      width:100%; background:#0d1a36; color:#eaf2ff; border:1px solid #2b3b63; border-radius:10px; padding:10px 12px;
      font:inherit; resize:vertical; min-height:110px;
    }
    .check{display:flex;align-items:center;gap:8px;font-size:14px}
    .radios{display:flex;gap:8px;flex-wrap:wrap}
    .radio-pill{
      display:inline-flex;align-items:center;gap:8px;padding:10px 12px;border-radius:999px;
      border:1px solid #2b3b63;background:#0d1a36;cursor:pointer;font-weight:800
    }
    .radio-pill input{accent-color:#3b82f6}

    /* Alerts */
    .alert{border:1px solid #fbbf2444;background:#f59e0b1a;color:#fde68a;padding:10px 12px;border-radius:10px;font-weight:700;margin-bottom:12px}
    .success{border:1px solid #10b98155;background:#10b9811a;color:#b8f3df}
    .error{color:#ffbdbd;font-size:12px;margin-top:-6px}

    /* Footer link */
    .back{margin-top:8px}
  </style>
</head>
<body>
@php
  // Sécurise si $order vient en array (session) : on garde accès objet.
  $order = is_array($order ?? null) ? (object) $order : $order;
  $clientName = optional($order->user)->name ?? 'Client';

  $statusMap = [
    'en_attente' => ['badge'=>'status-wait','label'=>'En attente'],
    'en_cours'   => ['badge'=>'status-wait','label'=>'En cours'],
    'valide'     => ['badge'=>'status-ok','label'=>'Validée'],
    'rejete'     => ['badge'=>'status-ko','label'=>'Rejetée'],
    'annulee'    => ['badge'=>'status-ko','label'=>'Annulée'],
  ];
  $st = $statusMap[$order->status ?? 'en_attente'] ?? $statusMap['en_attente'];

  $isValidated = (($order->status ?? null) === 'valide');
  $isRejected  = (($order->status ?? null) === 'rejete');
  $hasCashier  = !is_null($order->cashier_no ?? null);

  // si le contrôleur fournit $items enrichis, on s'en sert pour compter, sinon fallback
  $countItems  = isset($items) ? $items->count() : ($order->count ?? (is_countable($order->items ?? []) ? count($order->items) : 0));
  $createdAt   = $order->created_at ?? null;
  $createdTxt  = $createdAt
    ? (is_string($createdAt) ? \Carbon\Carbon::parse($createdAt)->format('d/m/Y H:i') : optional($createdAt)->format('d/m/Y H:i'))
    : '—';

  $prescriptionPath = $order->prescription_path ?? null;
  $prescriptionOriginalName = $order->prescription_original_name ?? null;
  $prescriptionExtension = $prescriptionPath ? strtolower(pathinfo($prescriptionPath, PATHINFO_EXTENSION)) : null;
  $prescriptionIsPdf = $prescriptionExtension === 'pdf';
  $prescriptionIsImage = in_array($prescriptionExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
  $prescriptionUrl = $prescriptionPath ? route('pharmacist.orders.prescription', $order) : null;
@endphp

  <main class="wrap">

    @if (session('error'))
      <div class="alert">{{ session('error') }}</div>
    @endif
    @if (session('success'))
      <div class="alert success">{{ session('success') }}</div>
    @endif

    <div class="card">
      <div class="head">
        <div style="min-width:260px">
          <h1 class="title">Commande #{{ $order->number ?? '—' }}</h1>
          <div class="sub">
            <span class="tag">👤 <strong>{{ $clientName }}</strong></span>
            <span class="tag">🕒 {{ $createdTxt }}</span>
            @if(!empty($order->pharmacy_name))
              <span class="tag">🏥 {{ $order->pharmacy_name }}</span>
            @endif
            @if(!is_null($order->cashier_no ?? null))
              <span class="tag">💳 Caisse N°{{ $order->cashier_no }}</span>
            @endif
          </div>

          <!-- Mini KPIs -->
          <div class="kpis">
            <div class="kpi">
              <div class="label">Articles</div>
              <div class="value">{{ $countItems }}</div>
            </div>
            <div class="kpi">
              <div class="label">Montant</div>
              <div class="value">{{ number_format((int)($order->total ?? 0), 0, ',', ' ') }} F</div>
            </div>
            <div class="kpi">
              <div class="label">Statut</div>
              <div class="value"><span class="badge {{ $st['badge'] }}">{{ $st['label'] }}</span></div>
            </div>
            <div class="kpi">
              <div class="label">Référence</div>
              <div class="value">#{{ $order->id ?? '—' }}</div>
            </div>
          </div>
        </div>

        <div>
          <span class="badge {{ $st['badge'] }}">{{ $st['label'] }}</span>
        </div>
      </div>

      <div class="grid">
        <!-- PRODUITS -->
        <section class="section card">
          <h2>Détails des produits</h2>
          <table class="table" role="table" aria-label="Produits de la commande">
            <thead>
            <tr>
              <th>Produit</th>
              <th style="width:80px">Qté</th>
              <th style="width:160px">Prix unitaire</th>
              <th style="width:160px">Total ligne</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items ?? collect() as $it)
              <tr>
                <td>
                  <div>{{ $it['name'] ?? '-' }}</div>
                  <div class="muted" style="font-size:11px">
                    ID produit : #{{ $it['product_id'] ?? ($it['id'] ?? '?') }}
                  </div>
                </td>
                <td>{{ $it['qty'] ?? 1 }}</td>
                <td>
                  {{ isset($it['unit_price']) ? number_format((float)$it['unit_price'], 0, ',', ' ') : '—' }} F
                </td>
                <td>
                  {{ isset($it['line_total']) ? number_format((float)$it['line_total'], 0, ',', ' ') : '—' }} F
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="muted">
                  Aucun produit enregistré pour cette commande.
                </td>
              </tr>
            @endforelse
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" style="text-align:right">Total</td>
                <td>{{ number_format((int)($order->total ?? 0), 0, ',', ' ') }} F</td>
              </tr>
            </tfoot>
          </table>

          @if($prescriptionUrl)
            <div class="hr"></div>
            <div>
              <h2>Ordonnance téléversée</h2>
              <p class="muted" style="margin-top:-2px">
                {{ $prescriptionOriginalName ?: basename($prescriptionPath) }}
              </p>

              @if($prescriptionIsPdf)
                <div style="margin-top:12px; border:1px solid var(--line); border-radius:14px; overflow:hidden; background:#081225">
                  <iframe
                    src="{{ $prescriptionUrl }}"
                    title="Ordonnance du client"
                    style="width:100%; height:560px; border:0; display:block; background:#fff;"
                  ></iframe>
                </div>
              @elseif($prescriptionIsImage)
                <div style="margin-top:12px; border:1px solid var(--line); border-radius:14px; overflow:hidden; background:#081225; padding:12px; text-align:center;">
                  <img
                    src="{{ $prescriptionUrl }}"
                    alt="Ordonnance du client"
                    style="max-width:100%; height:auto; border-radius:10px; display:inline-block; background:#fff;"
                  >
                </div>
              @else
                <div class="alert" style="margin-top:12px">
                  Le format de ce fichier ne peut pas être prévisualisé directement.
                </div>
              @endif

              <div style="margin-top:12px">
                <a href="{{ $prescriptionUrl }}" target="_blank" rel="noopener" class="btn outline">📎 Ouvrir l’ordonnance</a>
              </div>
            </div>
          @endif

          @if(!empty($order->decision_comment))
            <div class="hr"></div>
            <div>
              <h2>Message de la pharmacie</h2>
              <p class="muted" style="white-space:pre-line">{{ $order->decision_comment }}</p>
            </div>
          @endif
        </section>

        <!-- ACTIONS -->
        <aside class="aside">
          <!-- VALIDER -->
          @php $disableValidate = $isValidated || $isRejected; @endphp
          <div class="card {{ $disableValidate ? 'disabled' : '' }}">
            <h2>Valider la commande</h2>
            <p class="muted" style="margin-top:-4px">Choisissez une caisse et, si besoin, ajoutez la note d’ordonnance.</p>
            <form class="form" method="POST" action="{{ route('pharmacist.orders.updateStatus', ['order'=>$order->id]) }}">
              @csrf
              <input type="hidden" name="decision" value="valide">

              <div>
                <div class="muted" style="margin-bottom:6px">Caisse de paiement (obligatoire)</div>
                <div class="radios">
                  @for($i=1;$i<=5;$i++)
                    <label class="radio-pill">
                      <input type="radio" name="cashier_no" value="{{ $i }}" {{ $disableValidate ? 'disabled' : 'required' }}>
                      Caisse N°{{ $i }}
                    </label>
                  @endfor
                </div>
                @error('cashier_no')
                  <div class="error">{{ $message }}</div>
                @enderror
              </div>

              <label class="check">
                <input type="checkbox" name="with_prescription" value="1" {{ $disableValidate ? 'disabled' : '' }}>
                <span>Ajouter : « Veuillez venir avec votre ordonnance. »</span>
              </label>

              <button class="btn ok" type="submit" {{ $disableValidate ? 'disabled' : '' }}>
                ✅ Valider & notifier
              </button>

              @if($isValidated)
                <div class="muted" style="margin-top:8px">Déjà validée (caisse N°{{ $order->cashier_no ?? '—' }}).</div>
              @elseif($isRejected)
                <div class="muted" style="margin-top:8px">Commande rejetée : validation indisponible.</div>
              @else
                <div class="muted" style="margin-top:8px">Le client recevra une notification et un e-mail avec la caisse choisie.</div>
              @endif
            </form>
          </div>

          <!-- REJETER -->
          @php $disableReject = $hasCashier || $isValidated; @endphp
          <div class="card {{ $disableReject ? 'disabled' : '' }}">
            <h2>Rejeter la commande</h2>
            <p class="muted" style="margin-top:-4px">Un motif est obligatoire. Le rejet est impossible si une caisse est déjà assignée.</p>
            <form class="form" method="POST" action="{{ route('pharmacist.orders.updateStatus', ['order'=>$order->id]) }}">
              @csrf
              <input type="hidden" name="decision" value="rejete">

              <label for="reject-reason">Motif de rejet</label>
              <textarea id="reject-reason" name="comment" placeholder="Ex. : Produit indisponible, ordonnance non conforme, etc."
                        {{ $disableReject ? 'disabled' : 'required' }} minlength="5">{{ old('comment') }}</textarea>
              @error('comment')
                <div class="error">{{ $message }}</div>
              @enderror

              <button class="btn ko" type="submit" {{ $disableReject ? 'disabled' : '' }}>
                ❌ Rejeter & notifier
              </button>

              @if($hasCashier)
                <div class="muted" style="margin-top:8px">Caisse déjà assignée (N°{{ $order->cashier_no }}). Rejet non autorisé.</div>
              @elseif($isValidated)
                <div class="muted" style="margin-top:8px">Commande validée : rejet non autorisé.</div>
              @endif
            </form>
          </div>

          <div class="back">
            <a class="btn outline" href="{{ route('pharmacist.orders.index', ['status' => 'pending']) }}">← Retour à la liste</a>
          </div>
        </aside>
      </div>
    </div>
  </main>
</body>
</html>
