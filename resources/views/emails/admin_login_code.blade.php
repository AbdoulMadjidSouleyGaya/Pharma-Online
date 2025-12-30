@component('mail::message')
# Code de vérification

Bonjour,

Votre code de vérification administrateur est :

# **{{ $code }}**

Ce code expire dans **10 minutes**.

@component('mail::button', ['url' => route('admin.verify.form')])
Saisir mon code
@endcomponent

Merci,  
**PharmaOnline**
@endcomponent
