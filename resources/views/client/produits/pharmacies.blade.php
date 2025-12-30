@extends('layouts.ap')

@section('title', 'Choisir une pharmacie — Commande')

@section('header')
@endsection

@section('content')
@php
  $q = $q ?? request('q', '');
  $view = request('view', 'grid'); // grid | list

  $total = method_exists($pharmacies, 'total')
      ? (int) $pharmacies->total()
      : (isset($pharmacies) ? (int) $pharmacies->count() : 0);

  $isPaginated = method_exists($pharmacies, 'links');

  $prescriptionPath = session('prescription_path');
  $hasPrescription  = !empty($prescriptionPath);

  $rxTotal = (int)($rx_total ?? session('rx_total_terms', 0));
  $rxTerms = $rx_terms ?? session('rx_terms', []);
  if (!is_array($rxTerms)) $rxTerms = [];

  $rxOriginal   = session('prescription_original');
  $rxUploadedAt = session('prescription_uploaded_at');
@endphp

<style>
  .soft-ring:focus-within{ box-shadow: 0 0 0 4px rgba(16,185,129,.14); }
</style>

<div class="space-y-6">

  {{-- FLASH + ERRORS --}}
  <div class="space-y-3">
    @if (session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-extrabold">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="rounded-2xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-extrabold">
        {{ session('error') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="rounded-2xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
        <div class="font-extrabold">Erreur</div>
        <ul class="list-disc pl-5 mt-1 space-y-0.5">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>

  {{-- HERO --}}
  <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-emerald-500/10 blur-3xl"></div>
      <div class="absolute -left-24 -bottom-24 h-80 w-80 rounded-full bg-sky-500/10 blur-3xl"></div>
    </div>

    <div class="relative p-5 sm:p-7">
      <div class="flex flex-col gap-5">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
          <div class="space-y-1">
            <h1 class="text-xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
              Choisir une pharmacie partenaire
            </h1>
            <p class="text-sm text-slate-600 max-w-2xl">
              Sélectionnez une pharmacie pour consulter les produits disponibles, préparer votre panier et envoyer votre commande.
            </p>
          </div>

          <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700 border border-emerald-100">
              <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              Partenaires actifs
            </span>

            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1 text-xs font-extrabold text-slate-700 border border-slate-200">
              Total : <span class="text-slate-900">{{ number_format($total) }}</span>
            </span>

            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border
              {{ $hasPrescription ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
              Ordonnance :
              <span class="font-extrabold">{{ $hasPrescription ? 'OK' : 'Aucune' }}</span>
            </span>
          </div>
        </div>

        {{-- Résumé ordonnance --}}
        @if($rxTotal > 0 && count($rxTerms) > 0)
          <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
              <div>
                <div class="text-sm font-extrabold text-emerald-800">Recherche via ordonnance</div>
                <div class="text-xs text-emerald-800/80 mt-1">
                  Produits détectés :
                  <span class="font-extrabold">{{ count($rxTerms) }}</span>
                  @if($rxTotal > 0)
                    / <span class="font-extrabold">{{ $rxTotal }}</span>
                  @endif
                </div>
              </div>

              <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-extrabold text-slate-700 border border-emerald-100">
                  Tri : correspondance décroissante
                  <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                </span>
              </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
              @foreach($rxTerms as $term)
                <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[11px] font-extrabold text-slate-700 border border-emerald-100">
                  {{ $term }}
                </span>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </section>

  {{-- TOOLBAR --}}
  <section class="rounded-3xl border border-slate-200 bg-white shadow-sm p-4 sm:p-6 space-y-4">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
      <div>
        <h2 class="text-base font-extrabold text-slate-900">Recherche & affichage</h2>
        <p class="text-xs text-slate-500 mt-1">
          Filtrez par nom/quartier/adresse.
        </p>
      </div>

      <div class="flex items-center gap-2 flex-wrap">
        @if(!empty($q))
          <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1 text-xs font-bold text-slate-700 border border-slate-200">
            Résultats pour :
            <span class="font-extrabold text-slate-900">“{{ $q }}”</span>
          </span>
          <a href="{{ route('produits.commande.pharmacies') }}"
             class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
            Réinitialiser ✕
          </a>
        @endif
      </div>
    </div>

    <form action="{{ route('produits.commande.pharmacies') }}" method="get" class="space-y-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
        {{-- search --}}
        <div class="lg:col-span-8">
          <div class="soft-ring flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm
                      focus-within:border-emerald-500 transition">
            <span class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                  d="M12.9 14.32a8 8 0 111.414-1.414l3.387 3.386a1 1 0 01-1.414 1.415l-3.387-3.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z"
                  clip-rule="evenodd" />
              </svg>
            </span>

            <input
              type="text"
              name="q"
              value="{{ $q }}"
              class="w-full border-none bg-transparent focus:ring-0 text-sm placeholder:text-slate-400"
              placeholder="Ex : Pharmacie A, Yantala…"
              autocomplete="off">
          </div>
        </div>

        <div class="lg:col-span-12 flex justify-end">
          <button type="submit"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl bg-emerald-600 text-white text-xs font-extrabold hover:bg-emerald-500 transition shadow-sm hover:shadow">
            Rechercher
            <span aria-hidden="true">→</span>
          </button>
        </div>
      </div>
    </form>
  </section>

  {{-- ORDONNANCE --}}
  <section class="rounded-3xl border border-slate-200 bg-white shadow-sm p-4 sm:p-6 space-y-4">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
      <div>
        <h2 class="text-base font-extrabold text-slate-900">Ordonnance (PDF / image)</h2>
        <p class="text-xs text-slate-500 mt-1">
          Téléversez votre ordonnance, puis lancez la recherche automatique des pharmacies correspondantes.
        </p>

        @if($hasPrescription)
          <div class="mt-2 text-xs text-slate-600">
            <span class="font-extrabold text-slate-900">Fichier actuel :</span>
            <span class="font-bold">{{ $rxOriginal ?? basename($prescriptionPath) }}</span>
            @if($rxUploadedAt)
              <span class="text-slate-400">— téléversé le {{ $rxUploadedAt }}</span>
            @endif
          </div>
        @endif
      </div>

      <div class="flex items-center gap-2 flex-wrap">
        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border
          {{ $hasPrescription ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
          {{ $hasPrescription ? 'Ordonnance prête' : 'Aucune ordonnance' }}
          <span class="h-1.5 w-1.5 rounded-full {{ $hasPrescription ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
        </span>

        @if($rxTotal > 0 && count($rxTerms) > 0)
          <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1 text-xs font-extrabold text-slate-700 border border-slate-200">
            Produits détectés : <span class="text-slate-900">{{ count($rxTerms) }}</span>
          </span>
        @endif
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div class="text-[11px] text-slate-600 leading-snug">
          Formats acceptés : <span class="font-extrabold">PDF</span>, <span class="font-extrabold">Images</span> (JPG/JPEG/PNG/WEBP) — Taille max : <span class="font-extrabold">10 Mo</span>.
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          {{-- Upload --}}
          <form id="rxUploadForm" action="{{ route('produits.commande.ordonnance.upload') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="q" value="{{ $q }}">
            <input id="rxFile" type="file" name="prescription" class="hidden" accept="application/pdf,image/*">

            <button type="button"
              id="rxUploadBtn"
              class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl
                     bg-white border border-slate-200 text-slate-800 text-xs font-extrabold
                     hover:bg-slate-100 transition shadow-sm hover:shadow">
              {{ $hasPrescription ? 'Remplacer' : 'Téléverser' }}
            </button>
          </form>

          {{-- Retirer --}}
          @if($hasPrescription)
            <form action="{{ route('produits.commande.ordonnance.clear') }}" method="post">
              @csrf
              <input type="hidden" name="q" value="{{ $q }}">
              <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl
                       bg-white border border-red-200 text-red-700 text-xs font-extrabold
                       hover:bg-red-50 transition shadow-sm hover:shadow">
                Retirer
              </button>
            </form>
          @endif

          {{-- Search via ordonnance --}}
          <form action="{{ route('produits.commande.ordonnance.search') }}" method="post">
            @csrf
            <input type="hidden" name="q" value="{{ $q }}">

            <button type="submit"
              @if(!$hasPrescription) disabled @endif
              class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl
                     text-xs font-extrabold transition shadow-sm
                     {{ $hasPrescription
                        ? 'bg-emerald-600 text-white hover:bg-emerald-500 hover:shadow'
                        : 'bg-slate-200 text-slate-500 cursor-not-allowed' }}">
              Rechercher via ordonnance
            </button>
          </form>
        </div>
      </div>

      <script>
        (function(){
          const input = document.getElementById('rxFile');
          const form  = document.getElementById('rxUploadForm');
          const btn   = document.getElementById('rxUploadBtn');

          if(!input || !form || !btn) return;

          btn.addEventListener('click', function(){
            input.value = '';
            input.click();
          });

          input.addEventListener('change', function(){
            if(this.files && this.files.length > 0){
              form.submit();
            }
          });
        })();
      </script>
    </div>
  </section>

  {{-- EMPTY --}}
  @if($total === 0)
    <section class="rounded-3xl border border-dashed border-slate-200 bg-white py-12 px-5 text-center shadow-sm">
      <div class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-10a1 1 0 011 1v4a1 1 0 11-2 0V9a1 1 0 011-1zm0 8a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 16z" clip-rule="evenodd"/>
        </svg>
      </div>
      <p class="text-slate-800 text-sm font-extrabold">Aucune pharmacie trouvée.</p>
      <p class="text-slate-500 text-xs mt-1">Essayez un autre mot-clé (nom, quartier…).</p>
    </section>
  @else

    {{-- LIST / GRID --}}
    @if($view === 'list')
      <section class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-200 flex items-center justify-between gap-3 flex-wrap">
          <div class="text-sm font-extrabold text-slate-900">Pharmacies disponibles</div>
          <div class="text-xs text-slate-500">Cliquez sur “Voir les produits”</div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-slate-50">
              <tr class="text-left">
                <th class="px-4 py-3 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">Pharmacie</th>
                <th class="px-4 py-3 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">Quartier</th>
                <th class="px-4 py-3 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">Adresse</th>
                <th class="px-4 py-3 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">Contact</th>
                <th class="px-4 py-3 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">Correspondance</th>
                <th class="px-4 py-3 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pharmacies as $pharmacy)
                @php
                  $mapsQuery = trim(($pharmacy->name ?? '').' '.($pharmacy->district ?? '').' '.($pharmacy->address ?? '').' Niamey Niger');
                  $mapsUrl = $mapsQuery !== '' ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($mapsQuery) : null;

                  $mc = (int)($pharmacy->rx_match_count ?? 0);
                  $tt = (int)($pharmacy->rx_total_terms ?? 0);
                  $isPerfect = ($tt > 0 && $mc === $tt);
                @endphp

                <tr class="border-t border-slate-100 hover:bg-emerald-50/25 transition {{ $isPerfect ? 'bg-emerald-50/30' : '' }}">
                  <td class="px-4 py-3">
                    <div class="font-extrabold text-slate-900">{{ $pharmacy->name }}</div>
                    <div class="text-xs text-slate-500">Partenaire</div>
                  </td>
                  <td class="px-4 py-3 text-sm text-slate-700">{{ $pharmacy->district ?? '—' }}</td>
                  <td class="px-4 py-3 text-sm text-slate-700"><span class="line-clamp-2">{{ $pharmacy->address ?? '—' }}</span></td>
                  <td class="px-4 py-3 text-sm font-bold text-slate-700">{{ $pharmacy->phone ?? '—' }}</td>

                  <td class="px-4 py-3">
                    @if($tt > 0)
                      <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-extrabold border
                        {{ $isPerfect ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                        {{ $mc }}/{{ $tt }} produit(s)
                        @if($isPerfect)
                          <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        @endif
                      </span>
                    @else
                      <span class="text-xs text-slate-400">—</span>
                    @endif
                  </td>

                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                      <a href="{{ route('produits.commande.pharmacy', $pharmacy) }}"
                         class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl
                                bg-emerald-600 text-white text-xs font-extrabold hover:bg-emerald-500 transition shadow-sm hover:shadow">
                        Voir les produits →
                      </a>

                      @if($mapsUrl)
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center px-3 py-2 rounded-2xl border border-slate-200 bg-white
                                  text-slate-700 text-xs font-extrabold hover:bg-slate-50 transition shadow-sm hover:shadow"
                           title="Ouvrir sur Google Maps">
                          🗺️
                        </a>
                      @endif
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if($isPaginated)
          <div class="p-4 sm:p-6 border-t border-slate-200">
            {{ $pharmacies->appends(request()->query())->links() }}
          </div>
        @endif
      </section>

    @else
      {{-- GRID --}}
      <section class="space-y-3">
        <div class="flex items-center justify-between gap-3 flex-wrap">
          <div class="text-xs text-slate-600 font-bold">
            <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500 mr-2"></span>
            Cliquez sur une pharmacie pour afficher ses produits disponibles.
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          @foreach($pharmacies as $pharmacy)
            @php
              $mapsQuery = trim(($pharmacy->name ?? '').' '.($pharmacy->district ?? '').' '.($pharmacy->address ?? '').' Niamey Niger');
              $mapsUrl = $mapsQuery !== '' ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($mapsQuery) : null;
              $district = trim((string)($pharmacy->district ?? ''));

              $mc = (int)($pharmacy->rx_match_count ?? 0);
              $tt = (int)($pharmacy->rx_total_terms ?? 0);
              $isPerfect = ($tt > 0 && $mc === $tt);

              $found = $pharmacy->rx_match_terms ?? [];
              if (!is_array($found)) $found = [];
            @endphp

            <article class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-500 transition overflow-hidden {{ $isPerfect ? 'ring-1 ring-emerald-200' : '' }}">
              <div class="p-5 space-y-4">

                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <h3 class="text-sm font-extrabold text-slate-900 leading-tight break-words">
                      {{ $pharmacy->name }}
                    </h3>

                    <div class="mt-1 flex flex-wrap items-center gap-2">
                      <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 text-[10px] px-2 py-0.5 border border-emerald-100 font-extrabold">
                        Partenaire
                      </span>

                      @if($district !== '')
                        <span class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 text-[10px] px-2 py-0.5 border border-slate-200 font-extrabold">
                          {{ $district }}
                        </span>
                      @endif

                      @if($tt > 0)
                        <span class="inline-flex items-center rounded-full text-[10px] px-2 py-0.5 border font-extrabold
                          {{ $isPerfect ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                          {{ $mc }}/{{ $tt }} produits
                        </span>
                      @endif
                    </div>

                    @if(count($found) > 0)
                      <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach(array_slice($found, 0, 4) as $t)
                          <span class="inline-flex items-center rounded-full bg-white px-2 py-0.5 text-[10px] font-extrabold text-slate-700 border border-slate-200">
                            {{ $t }}
                          </span>
                        @endforeach
                        @if(count($found) > 4)
                          <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 text-[10px] font-extrabold text-slate-700 border border-slate-200">
                            +{{ count($found) - 4 }}
                          </span>
                        @endif
                      </div>
                    @endif
                  </div>

                  <div class="shrink-0">
                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1 text-[10px] font-extrabold text-slate-700 border border-slate-200">
                      Disponible
                      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    </span>
                  </div>
                </div>

                <div class="space-y-2 text-[12px] text-slate-600 leading-snug">
                  <div class="flex items-start gap-2">
                    <span class="text-slate-400 mt-[1px]">📍</span>
                    <span class="break-words">{{ $pharmacy->address ?? 'Adresse non renseignée' }}</span>
                  </div>

                  <div class="flex items-center gap-2">
                    <span class="text-slate-400">📞</span>
                    <span class="font-bold text-slate-700">{{ $pharmacy->phone ?? 'Contact non renseigné' }}</span>
                  </div>
                </div>

                <div class="pt-1 flex items-center gap-2">
                  <a href="{{ route('produits.commande.pharmacy', $pharmacy) }}"
                     class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl
                            bg-emerald-600 text-white text-xs font-extrabold hover:bg-emerald-500 transition shadow-sm hover:shadow">
                    Voir les produits
                    <span aria-hidden="true">→</span>
                  </a>

                  @if($mapsUrl)
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center px-3 py-2.5 rounded-2xl
                              border border-slate-200 bg-white text-slate-700 text-xs font-extrabold hover:bg-slate-50 transition shadow-sm hover:shadow"
                       title="Ouvrir sur Google Maps">
                      🗺️
                    </a>
                  @endif
                </div>
              </div>
            </article>
          @endforeach
        </div>

        @if($isPaginated)
          <div class="pt-2">
            {{ $pharmacies->appends(request()->query())->links() }}
          </div>
        @endif
      </section>
    @endif

  @endif
</div>
@endsection
