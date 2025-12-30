<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Alertes de stock</title>

    <style>
        :root{
            --bg:#020617;
            --ink:#e5e7eb;
            --muted:#9ca3af;
        }

        body{
            margin:0;
            min-height:100vh;
            background:#0f172a;
            font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
            color:var(--ink);
        }
        .wrap{max-width:1100px;margin:24px auto;padding:0 18px;}
        a{color:#93c5fd;text-decoration:none;}
        a:hover{text-decoration:underline;}
        h1{font-size:22px;font-weight:700;margin-bottom:6px;}
        p{margin:0 0 6px 0;}
        table{width:100%;border-collapse:collapse;font-size:13px;margin-top:14px;}
        th,td{padding:8px 9px;border-bottom:1px solid rgba(148,163,184,.4);}
        th{font-size:11px;text-transform:uppercase;color:#9ca3af;}
        .badge{
            display:inline-flex;
            padding:2px 8px;
            border-radius:999px;
            font-size:11px;
        }
        .badge-low{background:rgba(234,179,8,.2);color:#facc15;}
        .badge-out{background:rgba(248,113,113,.2);color:#fecaca;}
        .text-muted{color:#9ca3af;font-size:12px;}
        .top-links{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}

        .btn{
            border-radius:999px;
            border:1px solid rgba(148,163,184,.8);
            background:rgba(15,23,42,.8);
            color:#e5e7eb;
            padding:5px 11px;
            font-size:12px;
            font-weight:500;
            display:inline-flex;
            align-items:center;
            gap:4px;
            cursor:pointer;
            text-decoration:none;
        }
        .btn:hover{
            background:rgba(30,64,175,.9);
            border-color:rgba(129,140,248,.9);
        }

        /* Modal contact (même style que sur la page stock) */
        #contactModal{
            display:none;
            position:fixed;
            inset:0;
            background:rgba(15,23,42,.8);
            z-index:9999;
            align-items:center;
            justify-content:center;
        }
        #contactModal .modal-box{
            background:#020617;
            border-radius:16px;
            border:1px solid rgba(148,163,184,.6);
            padding:18px 18px 16px;
            width:100%;
            max-width:420px;
            color:#e5e7eb;
            box-shadow:0 25px 60px rgba(0,0,0,.6);
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
<div class="wrap">

    <div class="top-links">
        <a href="{{ route('pharmacist.stock.index') }}">← Retour à la gestion de stock</a>
        <a href="{{ url('/pharmacist/manage') }}">Centre de gestion</a>
    </div>

    <h1>Alertes de stock</h1>
    <p class="text-muted">
        Les alertes non lues sont marquées comme lues dès l'ouverture de cette page.
    </p>

    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Produit</th>
            <th>Type</th>
            <th>Message</th>
            <th>Fournisseur</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($alerts as $alert)
            <tr>
                <td>{{ $alert->created_at?->format('d/m/Y H:i') }}</td>
                <td>{{ $alert->product?->libelle ?? 'Produit supprimé' }}</td>
                <td>
                    @if($alert->level === 'low')
                        <span class="badge badge-low">Stock bas</span>
                    @else
                        <span class="badge badge-out">Rupture</span>
                    @endif
                </td>
                <td>{{ $alert->message }}</td>
                <td>
                    @if($alert->product && $alert->product->supplier)
                        <div>{{ $alert->product->supplier->name }}</div>
                        @if($alert->product->supplier->email)
                            <span class="text-muted">{{ $alert->product->supplier->email }}</span>
                        @endif
                    @else
                        <span class="text-muted">Aucun fournisseur</span>
                    @endif
                </td>
                <td>
                    @if($alert->product && $alert->product->supplier && $alert->product->supplier->email)
                        <button
                            type="button"
                            class="btn"
                            onclick="openContactModal({{ $alert->product->id }}, {{ $alert->product->supplier->id }}, @json($alert->product->libelle))"
                        >
                            📩 Contacter
                        </button>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#9ca3af;padding:10px;">
                    Aucune alerte pour le moment.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:10px;">
        {{ $alerts->links() }}
    </div>
</div>

{{-- 📩 Modal de contact fournisseur (partagé avec la page stock) --}}
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
                <button type="submit" class="btn">Envoyer</button>
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
