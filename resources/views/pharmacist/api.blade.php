@extends('layouts.ap')

@section('title', 'Gestion API')

@section('header')
<div class="relative overflow-hidden rounded-2xl border border-slate-100 bg-gradient-to-r from-emerald-500 via-emerald-600 to-sky-500 px-5 py-4 shadow-md">
  <div class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-white/15 blur-3xl"></div>
  <div class="pointer-events-none absolute -left-10 bottom-0 h-24 w-24 rounded-full bg-emerald-900/20 blur-2xl"></div>

  <div class="relative flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div class="space-y-1.5">
      <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-[11px] font-medium text-emerald-50 shadow-sm">
        <span class="flex h-4 w-4 items-center justify-center rounded-full bg-emerald-400/90 text-[10px] text-emerald-950">
          API
        </span>
        <span>Passerelle AbdouPharma</span>
      </div>

      <h1 class="text-2xl md:text-3xl font-extrabold text-white flex items-center gap-2">
        Connexion à l’API AbdouPharma
      </h1>

      <p class="text-xs md:text-sm text-emerald-50/90">
        URL centrale :
        <span class="inline-flex items-center gap-1 rounded-full bg-black/15 px-2 py-0.5 font-mono text-[11px]">
          {{ config('services.abdoupharma.base') }}
        </span>
      </p>
    </div>

    <div class="flex flex-col items-end gap-2">
      <a href="{{ route('pharmacist.dashboard') }}"
         class="inline-flex items-center gap-2 rounded-xl bg-white/95 px-3 py-2 text-xs md:text-sm font-semibold text-slate-900 shadow hover:bg-white">
        ← Retour au tableau de bord
      </a>

      <div class="inline-flex items-center gap-1 rounded-full bg-emerald-900/25 px-3 py-1 text-[11px] font-medium text-emerald-50">
        @if(!empty($pharmacy->api_token))
          <span class="h-2 w-2 rounded-full bg-lime-300 animate-pulse"></span>
          <span>Token configuré par l’administrateur</span>
        @else
          <span class="h-2 w-2 rounded-full bg-red-300"></span>
          <span>Token non configuré – contacte l’administrateur</span>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')

@if(session('ok'))
  <div class="mt-4 mb-4 rounded-xl bg-emerald-50 text-emerald-800 px-4 py-3 text-sm ring-1 ring-emerald-200 flex items-start gap-2">
    <span class="mt-0.5">✅</span>
    <div>{{ session('ok') }}</div>
  </div>
@endif

@if(session('hint'))
  <div class="mt-4 mb-4 rounded-xl bg-sky-50 text-sky-800 px-4 py-3 text-sm ring-1 ring-sky-200 flex items-start gap-2">
    <span class="mt-0.5">💡</span>
    <div>{{ session('hint') }}</div>
  </div>
@endif

<div class="mt-2 rounded-3xl border border-slate-100 bg-gradient-to-br from-slate-50 via-white to-emerald-50/40 p-5 md:p-6 shadow-sm">

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Colonne gauche : résumé --}}
    <div class="space-y-4">
      <div class="rounded-2xl border border-slate-100 bg-white/90 shadow-sm p-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-2 flex items-center gap-2">
          📊 État de la connexion
        </h2>
        <dl class="space-y-2 text-sm">
          <div class="flex justify-between">
            <dt class="text-xs text-slate-500 uppercase tracking-wide">Pharmacie</dt>
            <dd class="text-right text-slate-900 font-medium">
              {{ $pharmacy->name ?? 'Non définie' }}
            </dd>
          </div>
          <div class="flex justify-between items-center">
            <dt class="text-xs text-slate-500 uppercase tracking-wide">Token API</dt>
            <dd class="text-right text-xs">
              @if(!empty($pharmacy->api_token))
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px] px-2.5 py-0.5">
                  ● Configuré
                </span>
              @else
                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 text-red-700 border border-red-100 text-[11px] px-2.5 py-0.5">
                  ● Manquant
                </span>
              @endif
            </dd>
          </div>
        </dl>
        <p class="mt-3 text-[11px] text-slate-500">
          Le token est configuré exclusivement par l’administrateur dans le back-office.
        </p>
      </div>
    </div>

    {{-- Colonne droite : actions --}}
    <div class="xl:col-span-2 space-y-5">

      <div class="rounded-2xl border border-slate-100 bg-white/95 shadow-sm p-5 md:p-6">
        <div class="flex items-start justify-between gap-3 mb-3">
          <div>
            <h2 class="text-sm font-semibold text-slate-900 mb-1">
              🚀 Tests & synchronisation
            </h2>
            <p class="text-xs text-slate-600">
              Utilise ces actions pour vérifier la connexion et synchroniser le catalogue.
            </p>
          </div>
        </div>

        <div class="flex flex-wrap gap-3">

          {{-- 🔌 Tester la connexion --}}
          <form action="{{ route('pharmacist.api.test') }}" method="post">
            @csrf
            <button type="submit"
                    @if(empty($pharmacy->api_token)) disabled @endif
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 disabled:bg-slate-400 disabled:cursor-not-allowed">
              🔌 Tester la connexion
            </button>
          </form>

          {{-- 🔄 Synchroniser les produits --}}
          <form action="{{ route('pharmacist.api.load') }}" method="post"
                onsubmit="return confirm('Lancer la synchronisation des produits depuis AbdouPharma ?');">
            @csrf
            <button type="submit"
                    @if(empty($pharmacy->api_token)) disabled @endif
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-500 disabled:bg-slate-400 disabled:cursor-not-allowed">
              🔄 Synchroniser les produits
            </button>
          </form>

          {{-- 👀 Voir les produits synchronisés --}}
          <form action="{{ route('pharmacist.api.products') }}" method="get">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500">
              👀 Voir les produits synchronisés
            </button>
          </form>
        </div>

        @if(empty($pharmacy->api_token))
          <p class="mt-4 text-[11px] text-red-600 font-medium">
            Aucun token API n’est configuré pour ta pharmacie. Merci de contacter l’administrateur pour qu’il renseigne le token AbdouPharma.
          </p>
        @endif
      </div>
    </div>

  </div>
</div>
@endsection
