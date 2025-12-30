<!DOCTYPE html>
<html>
<body style="font-family:Arial, sans-serif;">

<h2>Demande de réapprovisionnement</h2>

<p>
Bonjour <strong>{{ $msg->supplier->name }}</strong>,
</p>

<p>La pharmacie <strong>{{ $msg->user->pharmacy->name }}</strong> vous contacte concernant le produit suivant :</p>

<p>
Produit : <strong>{{ $msg->product->libelle }}</strong><br>
ID : {{ $msg->product->id }}
</p>

<p>
Message du pharmacien :
</p>

<div style="background:#f1f5f9;padding:10px;border-radius:6px;">
    {!! nl2br(e($msg->message)) !!}
</div>

<p>Cordialement,<br>
<strong>{{ $msg->user->name }}</strong><br>
Pharmacie : {{ $msg->user->pharmacy->name }}</p>

</body>
</html>
