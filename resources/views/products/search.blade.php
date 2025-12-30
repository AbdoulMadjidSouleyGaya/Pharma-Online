@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-bold mb-2">Recherche d’un produit</h1>
  <p class="text-gray-600 mb-6">Tape le nom d’un médicament pour commencer (fonctionnel très bientôt).</p>

  <form action="{{ route('products.search') }}" method="get" class="flex gap-3">
    <input type="text" name="q" value="{{ $q }}" placeholder="Ex: Paracétamol 500mg"
           class="flex-1 rounded-xl border-gray-300" />
    <button class="px-5 py-2 rounded-xl bg-emerald-500 text-white">Rechercher</button>
  </form>

  @if($q)
    <div class="mt-6 rounded-2xl p-6 bg-white shadow">
      <p class="text-gray-700">Résultats pour : <strong>{{ $q }}</strong></p>
      <p class="text-gray-500 text-sm">Ici s’afficheront les produits disponibles par pharmacie.</p>
    </div>
  @endif

  <div class="mt-6">
    <a href="{{ route('home') }}" class="underline">← Retour à l’accueil</a>
  </div>
</div>
@endsection
