@if(session('success'))
    <div style="color: green; font-weight: bold;">
        {{ session('success') }}
    </div>
@endif

<p>Vous avez reçu un nouveau message de la part de : <strong>{{ $name }}</strong></p>
<p>Email : {{ $email }}</p>
<p>Message :</p>
<p>{{ $user_message }}</p> <!-- Utiliser user_message au lieu de message -->