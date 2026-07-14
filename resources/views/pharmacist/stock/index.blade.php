<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Gestion de stock — {{ $pharmacy->name }}</title>

    <style>
        :root{
            --blue:#2563eb; --blue2:#1d4ed8; --blue3:#60a5fa;
            --bg1:#020617; --bg2:#111827;
            --ink:#e5e7eb; --muted:#94a3b8;
            --radius:18px; --shadow:0 20px 45px rgba(0,0,0,.35);
        }

        *{box-sizing:border-box;margin:0;padding:0;}

        body{
            min-height:100vh;
            background:
                radial-gradient(circle at 20% 15%, #1e3a8a55, transparent 70%),
                radial-gradient(circle at 80% 0%, #0ea5e955, transparent 70%),
                radial-gradient(circle at 50% 100%, #10b98133, transparent 70%),
                linear-gradient(180deg, var(--bg1), var(--bg2));
            background-attachment: fixed;
            font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
            color:var(--ink);
        }

        .topbar{
            position:sticky;top:0;z-index:40;
            background:rgba(15,23,42,0.9);
            backdrop-filter:blur(18px);
            border-bottom:1px solid rgba(148,163,184,.35);
        }
        .topbar-in{
            max-width:1180px;margin:0 auto;
            padding:10px 18px;
            display:flex;align-items:center;justify-content:space-between;
            gap:12px;
        }
        .brand{display:flex;align-items:center;gap:10px}
        .brand-logo{
            width:38px;height:38px;border-radius:14px;
            background:radial-gradient(circle at 30% 20%, #38bdf8 0%, #0f172a 60%);
            border:1px solid rgba(255,255,255,.25);
            display:grid;place-items:center;font-size:18px;
        }
        .brand-title{font-weight:700;font-size:15px;letter-spacing:.04em;text-transform:uppercase;}
        .brand-sub{font-size:12px;color:rgba(255,255,255,.55);line-height:1.3}

        .top-actions{
            display:flex;align-items:center;gap:8px;flex-wrap:wrap;
        }

        .btn{
            border:1px solid rgba(148,163,184,.5);
            background:rgba(15,23,42,.9);
            color:#fff;padding:7px 13px;border-radius:999px;
            font-size:12px;font-weight:500;
            display:inline-flex;align-items:center;gap:6px;
            cursor:pointer;transition:.14s;
            text-decoration:none;
            white-space:nowrap;
        }
        .btn span{font-size:14px;}
        .btn-primary{
            background:linear-gradient(135deg,var(--blue2),var(--blue3));
            border-color:transparent;
        }
        .btn:hover{background:rgba(30,64,175,.9);transform:translateY(-1px);}
        .btn-primary:hover{filter:brightness(1.06);}

        .wrap{
            max-width:1180px;margin:22px auto 26px;
            padding:0 18px 30px;
        }

        .page-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-end;
            gap:10px;
            margin-bottom:10px;
        }
        .page-title{
            font-size:24px;
            font-weight:700;
        }
        .page-sub{
            color:var(--muted);
            font-size:13px;
            margin-top:3px;
        }
        .page-badge{
            font-size:11px;
            padding:4px 9px;
            border-radius:999px;
            border:1px solid rgba(148,163,184,.5);
            color:var(--muted);
        }

        .info-alert{
            background:rgba(234,179,8,.12);
            border:1px solid rgba(234,179,8,.5);
            padding:9px 13px;
            border-radius:12px;
            color:#facc15;
            font-size:12px;
            margin-bottom:18px;
            display:flex;
            gap:8px;
            align-items:flex-start;
        }
        .info-alert strong{font-weight:600;}

        .flash{
            padding:9px 12px;
            border-radius:12px;
            margin-bottom:14px;
            font-size:13px;
        }
        .flash-success{
            background:rgba(22,163,74,.2);
            border:1px solid rgba(22,163,74,.5);
            color:#bbf7d0;
        }
        .flash-error{
            background:rgba(239,68,68,.2);
            border:1px solid rgba(239,68,68,.5);
            color:#fecaca;
        }

        .card{
            background:rgba(15,23,42,0.88);
            border:1px solid rgba(148,163,184,.4);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            padding:18px 18px 16px;
            backdrop-filter:blur(10px);
        }

        .stats-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:14px;
            margin-bottom:18px;
        }
        .stat-box{
            background:rgba(15,23,42,.8);
            border-radius:14px;
            padding:11px 13px;
            border:1px solid rgba(148,163,184,.45);
        }
        .stat-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;}
        .stat-value{font-size:22px;font-weight:700;margin-top:4px;}
        .stat-hint{font-size:11px;color:rgba(148,163,184,.85);margin-top:2px;}

        .toolbar{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            align-items:flex-start;
            justify-content:space-between;
            margin-bottom:14px;
            margin-top:4px;
        }

        .toolbar-left,
        .toolbar-right{
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            align-items:center;
        }

        .field-group{
            display:flex;
            flex-direction:column;
            gap:3px;
        }
        .field-label{
            font-size:11px;
            color:var(--muted);
        }

        .input, .select, .input-sm{
            border-radius:999px;
            border:1px solid rgba(148,163,184,.9);
            background:rgba(15,23,42,.9);
            color:var(--ink);
            padding:6px 12px;
            font-size:13px;
            outline:none;
            min-height:32px;
        }
        .input::placeholder{color:var(--muted);}
        .select{
            padding-right:26px;
        }
        .input-sm{
            font-size:12px;
            padding:5px 10px;
            min-width:80px;
        }

        .table-wrap{
            overflow-x:auto;
            border-radius:var(--radius);
            border:1px solid rgba(148,163,184,.45);
            background:rgba(15,23,42,.95);
        }
        table{
            width:100%;
            border-collapse:collapse;
            font-size:13px;
        }
        thead{
            background:rgba(15,23,42,1);
        }
        th,td{
            padding:9px 10px;
            text-align:left;
            border-bottom:1px solid rgba(30,64,175,.38);
        }
        th{
            font-weight:600;
            font-size:11px;
            color:var(--muted);
            text-transform:uppercase;
            letter-spacing:.06em;
        }
        tr:hover td{
            background:rgba(15,23,42,.9);
        }

        .badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:3px 9px;
            border-radius:999px;
            font-size:10px;
            font-weight:600;
        }
        .badge-ok{
            background:rgba(22,163,74,.18);
            color:#86efac;
            border:1px solid rgba(22,163,74,.4);
        }
        .badge-low{
            background:rgba(234,179,8,.16);
            color:#facc15;
            border:1px solid rgba(234,179,8,.45);
        }
        .badge-out{
            background:rgba(248,113,113,.16);
            color:#fecaca;
            border:1px solid rgba(248,113,113,.45);
        }

        .text-muted{color:var(--muted);font-size:12px;}
        .text-right{text-align:right;}
        .text-center{text-align:center;}

        /* ✅ Pagination custom sans grosses flèches */
        .pagination-custom{
            margin-top:14px;
            display:flex;
            justify-content:flex-end;
            gap:6px;
            font-size:12px;
        }
        .pagination-custom a,
        .pagination-custom span{
            padding:4px 9px;
            border-radius:999px;
            border:1px solid rgba(148,163,184,.45);
            text-decoration:none;
            color:var(--ink);
        }
        .pagination-custom a:hover{
            background:rgba(37,99,235,.85);
            border-color:#1d4ed8;
        }
        .pagination-custom .is-current{
            background:#2563eb;
            border-color:#1d4ed8;
            font-weight:600;
        }

        /* Modal contact fournisseur */
        #contactModal{
            display:none;
            position:fixed;
            inset:0;
            background:rgba(15,23,42,.85);
            z-index:9999;
            align-items:center;
            justify-content:center;
        }
        #contactModal .modal-box{
            background:#020617;
            border-radius:16px;
            border:1px solid rgba(148,163,184,.8);
            padding:18px 18px 16px;
            width:100%;
            max-width:420px;
            color:#e5e7eb;
            box-shadow:0 25px 60px rgba(0,0,0,.7);
        }
        #contactModal h3{
            margin:0 0 8px;
            font-size:18px;
            font-weight:700;
        }
        #contactModal label{
            display:block;
            font-size:13px;
            margin:10px 0 4px;
            color:#cbd5f5;
        }
        #contactModal .input,
        #contactModal textarea{
            width:100%;
            border-radius:12px;
            border:1px solid rgba(148,163,184,.8);
            background:#020617;
            color:#e5e7eb;
            padding:6px 10px;
            font-size:13px;
        }
        #contactModal textarea{
            border-radius:14px;
            min-height:90px;
            resize:vertical;
        }
        #contactModal .actions{
            margin-top:12px;
            display:flex;
            justify-content:flex-end;
            gap:8px;
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="topbar-in">
        <div class="brand">
            <div class="brand-logo">📦</div>
            <div>
                <div class="brand-title">Gestion de stock</div>
                <div class="brand-sub">
                    {{ $pharmacy->name }}<br>
                    Connecté en tant que <strong>{{ Auth::user()->name }}</strong>
                </div>
            </div>
        </div>

        <div class="top-actions">
            <a class="btn" href="{{ url('/pharmacist/manage') }}">
                Centre de gestion
            </a>
            <a class="btn" href="{{ route('pharmacist.dashboard') }}">
               Tableau de bord
            </a>
            <a class="btn" href="{{ route('pharmacist.suppliers.index') }}">
                Fournisseurs
            </a>
            <a class="btn" href="{{ route('pharmacist.stock.export') }}">
               Exporter Excel
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                Imprimer
            </button>
        </div>
    </div>
</header>

<div class="wrap">

    <div class="page-header">
        <div>
            <div class="page-title">Vue d’ensemble du stock</div>
            <div class="page-sub">
                Surveillez vos produits, les seuils d’alerte et enregistrez les entrées de stock.
            </div>
        </div>
        <div class="page-badge">
            Dernière mise à jour : {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- 🔔 Note sur le seuil minimum --}}
    <div class="info-alert">
        <div>⚠️</div>
        <div>
            <strong>Seuil minimum :</strong> fixé à <strong>20 unités</strong>. En dessous de ce seuil,
            le produit est considéré comme <strong>en stock bas</strong>.
        </div>
    </div>

    {{-- Messages flash --}}
    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <div class="card">

        {{-- Stats globales --}}
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Produits enregistrés</div>
                <div class="stat-value">{{ $totalProducts }}</div>
                <div class="stat-hint">Références actives dans cette pharmacie</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Stock bas</div>
                <div class="stat-value" style="color:#facc15;">{{ $lowStockCount }}</div>
                <div class="stat-hint">À surveiller pour réapprovisionnement</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Ruptures</div>
                <div class="stat-value" style="color:#fecaca;">{{ $outOfStockCount }}</div>
                <div class="stat-hint">Produits indisponibles à ce jour</div>
            </div>
        </div>

        {{-- Barre outils --}}
        <div class="toolbar">

            <form method="GET" class="toolbar-left">
                <div class="field-group">
                    <label class="field-label">Recherche</label>
                    <input class="input" type="text" name="q" value="{{ request('q') }}"
                           placeholder="Nom du produit, code…">
                </div>

                <div class="field-group">
                    <label class="field-label">État du stock</label>
                    <select class="select" name="state">
                        <option value="">Tous les états</option>
                        <option value="low" {{ request('state') === 'low' ? 'selected' : '' }}>Stock bas</option>
                        <option value="out" {{ request('state') === 'out' ? 'selected' : '' }}>Rupture</option>
                    </select>
                </div>

                <div class="field-group" style="margin-top:18px;">
                    <button class="btn" type="submit">
                        🔍 Filtrer
                    </button>
                </div>
            </form>

            {{-- Formulaire d’entrée de stock rapide --}}
            <form method="POST" action="{{ route('pharmacist.stock.entry.store') }}" class="toolbar-right">
                @csrf

                <div class="field-group">
                    <label class="field-label">Produit</label>
                    <select name="pharma_product_id" class="select" required>
                        <option value="">Sélectionner…</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->libelle }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field-group">
                    <label class="field-label">Fournisseur</label>
                    <select name="supplier_id" class="select">
                        <option value="">Aucun</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field-group">
                    <label class="field-label">Quantité</label>
                    <input class="input-sm" type="number" name="quantity" min="1" value="1" required>
                </div>

                <div class="field-group" style="min-width:180px;">
                    <label class="field-label">Commentaire (optionnel)</label>
                    <input class="input-sm" type="text" name="comment" placeholder="Réception fournisseur…">
                </div>

                <div class="field-group" style="margin-top:18px;">
                    <button class="btn btn-primary" type="submit">
                        ➕ Entrée stock
                    </button>
                </div>
            </form>

        </div>

        {{-- Tableau des produits --}}
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-right">Prix</th>
                    <th class="text-right">Quantité</th>
                    <th class="text-center">État</th>
                    <th>Fournisseur</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    @php
                        $qty = (int) $product->quantity;
                        $min = 20; // seuil fixe pour l'affichage
                        $state = 'Disponible';
                        $badgeClass = 'badge badge-ok';

                        if ($qty <= 0) {
                            $state = 'Rupture';
                            $badgeClass = 'badge badge-out';
                        } elseif ($qty <= $min) {
                            $state = 'Stock bas';
                            $badgeClass = 'badge badge-low';
                        }
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $product->libelle }}</div>
                            <div class="text-muted">#{{ $product->id }}</div>
                        </td>

                        <td class="text-right">
                            {{ number_format($product->prix, 0, ',', ' ') }} F CFA
                        </td>

                        <td class="text-right">
                            <span style="font-family:monospace;">{{ $qty }}</span>
                        </td>

                        <td class="text-center">
                            <span class="{{ $badgeClass }}">{{ $state }}</span>
                        </td>

                        <td>
                            @if($product->supplier)
                                {{-- Fournisseur lié en base locale --}}
                                <div style="font-size:13px;font-weight:500;">
                                    {{ $product->supplier->name }}
                                </div>
                                
                            @elseif(!empty($product->supplier_name))
                                {{-- Fournisseur issu directement d'AbdouPharma (champ texte) --}}
                                <div style="font-size:13px;font-weight:500;">
                                    {{ $product->supplier_name }}
                                </div>
                            @else
                                <span class="text-muted">Aucun fournisseur lié</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding:14px 10px;">
                            <span class="text-muted">Aucun produit trouvé pour cette pharmacie.</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- ✅ Pagination sans flèches --}}
        @if($products->hasPages())
            <div class="pagination-custom">
                @for ($page = 1; $page <= $products->lastPage(); $page++)
                    @if ($page == $products->currentPage())
                        <span class="is-current">{{ $page }}</span>
                    @else
                        <a href="{{ $products->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor
            </div>
        @endif

    </div>
</div>

{{-- 📩 Modal de contact fournisseur --}}
<div id="contactModal">
    <div class="modal-box">
        <h3>Contacter le fournisseur</h3>
        <p class="text-muted" style="margin-bottom:8px;font-size:12px;">
            Envoyez une demande de réapprovisionnement au fournisseur sélectionné.
        </p>

        <form method="POST" action="{{ route('pharmacist.stock.contact') }}">
            @csrf
            <input type="hidden" name="product_id" id="contact_product_id">
            <input type="hidden" name="supplier_id" id="contact_supplier_id">

            <label for="contact_subject">Sujet</label>
            <input
                id="contact_subject"
                type="text"
                name="subject"
                class="input"
                required
                value="Demande de réapprovisionnement"
            >

            <label for="contact_message">Message</label>
            <textarea
                id="contact_message"
                name="message"
                required
            ></textarea>

            <div class="actions">
                <button type="button" class="btn" onclick="closeContactModal()">Annuler</button>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openContactModal(productId, supplierId, productName) {
    document.getElementById('contact_product_id').value = productId;
    document.getElementById('contact_supplier_id').value = supplierId;

    const defaultMsg =
        "Bonjour,\n\n" +
        "Nous souhaitons réapprovisionner le produit suivant :\n" +
        "• " + productName + "\n\n" +
        "Merci de nous communiquer vos disponibilités et vos conditions (prix, délai de livraison).\n\n" +
        "Cordialement,\n" +
        "{{ Auth::user()->name }}";

    document.getElementById('contact_message').value = defaultMsg;

    document.getElementById('contactModal').style.display = "flex";
}

function closeContactModal() {
    document.getElementById('contactModal').style.display = "none";
}
</script>

</body>
</html>
