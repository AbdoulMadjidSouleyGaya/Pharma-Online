@extends('layouts.ap')

@section('title', 'Catalogue')

@section('content')
<div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-4 md:p-6">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h2 class="text-xl font-semibold tracking-tight">Produits</h2>
      <p class="text-sm text-gray-500">
        @if($products->total())
          Affichage {{ $products->firstItem() }}–{{ $products->lastItem() }} / {{ number_format($products->total(), 0, ',', ' ') }}
        @else
          Aucun résultat
        @endif
      </p>
    </div>

    <form method="get" class="w-full md:w-auto">
      <div class="grid grid-cols-1 md:grid-cols-6 gap-2 md:gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher libellé…"
               class="md:col-span-2 rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 px-3 py-2">

        <select name="stock" class="rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 px-3 py-2">
          <option value="">Stock (tous)</option>
          <option value="Disponible" @selected(request('stock')==='Disponible')>Disponible</option>
          <option value="Rupture" @selected(request('stock')==='Rupture')>Rupture</option>
        </select>

        <input type="number" step="0.01" min="0" name="min_prix" value="{{ request('min_prix') }}" placeholder="Prix min"
               class="rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 px-3 py-2">
        <input type="number" step="0.01" min="0" name="max_prix" value="{{ request('max_prix') }}" placeholder="Prix max"
               class="rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 px-3 py-2">

        <select name="per_page" class="rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 px-3 py-2">
          @foreach([10,15,20,50,100] as $pp)
            <option value="{{ $pp }}" @selected((int)request('per_page',15)===$pp)>{{ $pp }} / page</option>
          @endforeach
        </select>

        <button class="inline-flex items-center justify-center rounded-xl bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700 transition">
          Rechercher
        </button>
      </div>

      {{-- conserver tri courant sur submit --}}
      <input type="hidden" name="sort" value="{{ $sort ?? request('sort','libelle') }}">
      <input type="hidden" name="order" value="{{ $order ?? request('order','asc') }}">
    </form>
  </div>

  <div class="overflow-x-auto mt-5">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-gray-600">
          <th class="py-3 pr-3">#</th>
          <th class="py-3 pr-3">
            @php
              $curSort = $sort ?? request('sort','libelle');
              $curOrder = $order ?? request('order','asc');
              $link = function(array $kv) {
                  return url()->current() . '?' . http_build_query(array_merge(request()->query(), $kv));
              };
            @endphp
            <a href="{{ $link(['sort'=>'libelle','order'=>($curSort==='libelle' && $curOrder==='asc')?'desc':'asc','page'=>1]) }}"
               class="inline-flex items-center gap-1 hover:underline underline-offset-4">
              Libellé
              <span class="text-gray-400">
                {{ $curSort==='libelle' ? ($curOrder==='asc'?'↑':'↓') : '' }}
              </span>
            </a>
          </th>
          <th class="py-3 pr-3">
            <a href="{{ $link(['sort'=>'prix','order'=>($curSort==='prix' && $curOrder==='asc')?'desc':'asc','page'=>1]) }}"
               class="inline-flex items-center gap-1 hover:underline underline-offset-4">
              Prix (FCFA)
              <span class="text-gray-400">
                {{ $curSort==='prix' ? ($curOrder==='asc'?'↑':'↓') : '' }}
              </span>
            </a>
          </th>
          <th class="py-3 pr-3">
            <a href="{{ $link(['sort'=>'quantity','order'=>($curSort==='quantity' && $curOrder==='asc')?'desc':'asc','page'=>1]) }}"
               class="inline-flex items-center gap-1 hover:underline underline-offset-4">
              Quantité
              <span class="text-gray-400">
                {{ $curSort==='quantity' ? ($curOrder==='asc'?'↑':'↓') : '' }}
              </span>
            </a>
          </th>
          <th class="py-3 pr-3">
            <a href="{{ $link(['sort'=>'created_at','order'=>($curSort==='created_at' && $curOrder==='asc')?'desc':'asc','page'=>1]) }}"
               class="inline-flex items-center gap-1 hover:underline underline-offset-4">
              Créé le
              <span class="text-gray-400">
                {{ $curSort==='created_at' ? ($curOrder==='asc'?'↑':'↓') : '' }}
              </span>
            </a>
          </th>
          <th class="py-3">Stock</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($products as $p)
          <tr class="hover:bg-gray-50">
            <td class="py-3 pr-3 text-gray-500">{{ $p->id }}</td>
            <td class="py-3 pr-3 font-medium">{{ $p->libelle }}</td>
            <td class="py-3 pr-3">{{ number_format($p->prix, 2, ',', ' ') }}</td>
            <td class="py-3 pr-3">
              {{ $p->quantity ?? 0 }}
            </td>
            <td class="py-3 pr-3 text-gray-600">
              {{ optional($p->created_at)->format('Y-m-d H:i') }}
            </td>
            <td class="py-3">
              @if($p->stock === 'Disponible')
                <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-1 text-xs font-semibold ring-1 ring-emerald-600/20">Disponible</span>
              @else
                <span class="inline-flex items-center rounded-full bg-rose-50 text-rose-700 px-2.5 py-1 text-xs font-semibold ring-1 ring-rose-600/20">Rupture</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-10 text-center text-gray-500">
              Aucun produit ne correspond à votre recherche.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-5 flex items-center justify-between">
    <div class="text-sm text-gray-500">
      @if($products->total()) Page {{ $products->currentPage() }} / {{ $products->lastPage() }} @endif
    </div>
    <div class="flex items-center gap-2">
      {{ $products->links() }}
    </div>
  </div>
</div>
@endsection
