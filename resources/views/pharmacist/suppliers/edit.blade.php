<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Modifier fournisseur</title>

    <style>
        body{
            background:#0f172a;
            color:#e5e7eb;
            font-family:system-ui;
            padding:20px;
        }
        .card{
            background:rgba(255,255,255,.04);
            padding:25px;
            max-width:550px;
            margin:auto;
            border-radius:16px;
            border:1px solid rgba(255,255,255,.08);
            backdrop-filter:blur(4px);
        }
        label{
            font-size:13px;
            color:#94a3b8;
            margin-bottom:4px;
            display:block;
        }
        .input{
            width:100%;
            padding:10px;
            background:#020617;
            border:1px solid rgba(255,255,255,.1);
            border-radius:10px;
            color:white;
            margin-bottom:12px;
        }
        .btn{
            background:#2563eb;
            padding:10px 16px;
            border:none;
            border-radius:999px;
            color:white;
            cursor:pointer;
            font-size:13px;
        }
        .btn:hover{
            background:#1d4ed8;
        }
        a{
            color:#93c5fd;
            text-decoration:none;
            font-size:13px;
        }
        a:hover{text-decoration:underline;}
    </style>
</head>
<body>

<a href="{{ route('pharmacist.suppliers.index') }}">← Retour aux fournisseurs</a>

<div class="card">
    <h2 style="margin-bottom:15px;">✏️ Modifier le fournisseur</h2>

    <form method="POST" action="{{ route('pharmacist.suppliers.update', $supplier) }}">
        @csrf @method('PUT')

        <label>Raison sociale *</label>
        <input class="input" name="name" value="{{ $supplier->name }}" required>

        <label>Nom complet</label>
        <input class="input" name="contact" value="{{ $supplier->contact }}">

        <label>Téléphone</label>
        <input class="input" name="phone" value="{{ $supplier->phone }}">

        <label>Email</label>
        <input class="input" type="email" name="email" value="{{ $supplier->email }}">

        <label>Adresse</label>
        <input class="input" name="address" value="{{ $supplier->address }}">

        <button class="btn">💾 Enregistrer</button>
    </form>
</div>

</body>
</html>
