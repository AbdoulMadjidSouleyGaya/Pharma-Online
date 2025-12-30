<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Fournisseurs — {{ $pharmacy->name }}</title>

    <style>
        :root{
            --bg:#0f172a;
            --bg2:#1e293b;
            --ink:#e5e7eb;
            --muted:#94a3b8;
            --blue:#2563eb;
            --blue2:#1d4ed8;
            --red:#dc2626;
            --card:rgba(255,255,255,.03);
        }

        body{
            margin:0;
            font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
            background:var(--bg);
            color:var(--ink);
            min-height:100vh;
        }

        /* Top Navigation */
        .topbar{
            background:rgba(15,23,42,.85);
            backdrop-filter:blur(12px);
            padding:16px 20px;
            border-bottom:1px solid rgba(255,255,255,.06);
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .top-title{
            font-size:17px;
            font-weight:600;
        }
        .top-actions a{
            color:var(--ink);
            text-decoration:none;
            margin-left:14px;
            font-size:13px;
            padding:7px 14px;
            border-radius:999px;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.1);
        }
        .top-actions a:hover{
            background:rgba(255,255,255,.18);
        }

        .wrap{
            max-width:1150px;
            margin:20px auto;
            padding:0 20px;
        }

        .section-title{
            font-size:22px;
            font-weight:700;
            margin-bottom:5px;
        }
        .text-muted{
            color:var(--muted);
            font-size:13px;
        }

        /* Cards */
        .card{
            background:var(--card);
            backdrop-filter:blur(4px);
            padding:18px;
            margin-top:20px;
            border-radius:16px;
            border:1px solid rgba(255,255,255,.08);
        }

        /* Form */
        label{
            font-size:13px;
            color:var(--muted);
            margin-bottom:4px;
            display:block;
        }
        .input{
            width:100%;
            background:#020617;
            padding:10px 12px;
            border-radius:10px;
            border:1px solid rgba(255,255,255,.1);
            color:var(--ink);
            font-size:14px;
        }
        .form-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
            gap:14px;
            margin-top:8px;
        }
        .btn{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:9px 16px;
            border-radius:999px;
            border:none;
            color:white;
            background:var(--blue);
            cursor:pointer;
            font-size:13px;
            font-weight:500;
            text-decoration:none;
        }
        .btn:hover{
            background:var(--blue2);
        }

        /* Table */
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:15px;
            font-size:13px;
        }
        th{
            text-align:left;
            padding-bottom:8px;
            color:var(--muted);
            text-transform:uppercase;
            font-size:11px;
            border-bottom:1px solid rgba(255,255,255,.08);
        }
        td{
            padding:10px 0;
            border-bottom:1px solid rgba(255,255,255,.05);
        }
        .actions-cell{
            display:flex;
            gap:6px;
        }
        .btn-sm{
            padding:5px 10px;
            font-size:11px;
            border-radius:999px;
            background:rgba(255,255,255,.1);
            border:1px solid rgba(255,255,255,.15);
        }
        .btn-sm:hover{
            background:rgba(255,255,255,.2);
        }
        .btn-danger{
            background:var(--red) !important;
        }

        .flash{
            padding:10px;
            border-radius:12px;
            margin-top:14px;
            font-size:13px;
        }
        .flash-success{
            background:rgba(22,163,74,.2);
            border:1px solid rgba(22,163,74,.5);
            color:#bbf7d0;
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="top-title">📦 Gestion des fournisseurs</div>
    <div class="top-actions">
        <a href="{{ route('pharmacist.stock.index') }}">⬅️ Stock</a>
        <a href="{{ route('pharmacist.manage') }}">⚙️ Centre de gestion</a>
    </div>
</div>

<div class="wrap">

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    <h1 class="section-title">Fournisseurs</h1>
    <p class="text-muted">Ajouter et gérer vos fournisseurs pharmaceutiques.</p>

    <!-- Add Supplier -->
    <div class="card">
        <h3 style="font-size:17px;margin-bottom:10px;">➕ Ajouter un fournisseur</h3>

        <form method="POST" action="{{ route('pharmacist.suppliers.store') }}">
            @csrf

            <div class="form-grid">
                <div>
                    <label>Raison sociale *</label>
                    <input class="input" name="name" value="{{ old('name') }}" required>
                </div>

                <div>
                    <label>Nom complet (contact)</label>
                    <input class="input" name="contact" value="{{ old('contact') }}">
                </div>

                <div>
                    <label>Téléphone</label>
                    <input class="input" name="phone" value="{{ old('phone') }}">
                </div>

                <div>
                    <label>Email</label>
                    <input class="input" type="email" name="email" value="{{ old('email') }}">
                </div>

                <div style="grid-column:1 / -1;">
                    <label>Adresse</label>
                    <input class="input" name="address" value="{{ old('address') }}">
                </div>
            </div>

            <button class="btn" style="margin-top:15px;">💾 Enregistrer</button>
        </form>
    </div>

    <!-- List Suppliers -->
    <div class="card">
        <h3 style="font-size:17px;margin-bottom:10px;">📋 Liste des fournisseurs</h3>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Intitulé</th>
                {{-- 🔥 Colonne "Contact" retirée --}}
                <th>Téléphone</th>
                <th>Email</th>
                <th>Adresse</th>
                <th>Contacter</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    {{-- ✅ ID logique qui commence à 1 et suit la pagination --}}
                    <td>
                        {{ $suppliers->firstItem() + $loop->index }}
                    </td>

                    <td>{{ $supplier->name }}</td>

                    <td>{{ $supplier->phone ?: '—' }}</td>
                    <td>{{ $supplier->email ?: '—' }}</td>
                    <td>{{ $supplier->address ?: '—' }}</td>

                    <td>
                        <div class="actions-cell">
                            @if($supplier->phone)
                                <a class="btn-sm" href="tel:{{ $supplier->phone }}">📞</a>
                            @endif
                            @if($supplier->email)
                                <a class="btn-sm" href="mailto:{{ $supplier->email }}">✉️</a>
                            @endif
                        </div>
                    </td>

                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('pharmacist.suppliers.edit',$supplier) }}" class="btn-sm">✏️</a>

                            <form method="POST" action="{{ route('pharmacist.suppliers.destroy',$supplier) }}"
                                  onsubmit="return confirm('Supprimer ce fournisseur ?');">
                                @csrf @method('DELETE')
                                <button class="btn-sm btn-danger">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--muted);padding:10px;">
                        Aucun fournisseur enregistré.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:12px;">
            {{ $suppliers->links() }}
        </div>
    </div>

</div>

</body>
</html>
