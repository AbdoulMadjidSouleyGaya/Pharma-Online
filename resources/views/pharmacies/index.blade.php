@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-bold mb-2">Pharmacies @if($garde) de garde @endif</h1>
  <p class="text-gray-600 mb-6">
    Cette page affichera la liste des pharmacies @if($garde) de garde @endif (prochaines étapes).
  </p>

  <div class="rounded-2xl p-6 bg-white shadow">
    <p class="text-gray-700">Placeholder : bientôt la liste, le filtre par proximité, etc.</p>
  </div>

  <div class="mt-6">
    <a href="{{ route('home') }}" class="underline">← Retour à l’accueil</a>
  </div>
</div>
@endsection
