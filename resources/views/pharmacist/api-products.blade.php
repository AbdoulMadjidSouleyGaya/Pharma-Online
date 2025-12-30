@extends('layouts.ap')

@section('title', 'Produits synchronisés')

@section('header')
    @php
        $pharmacyName   = $pharmacy->name ?? 'Pharmacie';
        $totalProducts  = method_exists($products, 'total') 
                            ? $products->total() 
                            : $products->count();
    @endphp
    {{-- HEADER PREMIUM STYLE --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        {{-- Infos pharmacie --}}
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px] font-semibold uppercase tracking-wide">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Synchronisation AbdouPharma
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight flex items-center gap-2">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-700 text-lg">
                        💊
                    </span>
                    <span>Produits — {{ $pharmacyName }}</span>
                </h1>
            </div>

            <p class="text-sm text-slate-600">
                Visualisez l’inventaire synchronisé de votre stock en temps réel.
            </p>
        </div>

        {{-- Actions à droite --}}
        <div class="flex flex-col items-end gap-3">

            {{-- Compteur produits --}}
            <div class="inline-flex items-center gap-3 rounded-2xl bg-white border border-slate-200 shadow-sm px-4 py-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 rounded-xl bg-slate-900 text-white items-center justify-center text-xs font-bold">
                        {{ $totalProducts }}
                    </span>
                    <div class="flex flex-col leading-tight text-xs text-slate-600">
                        <span class="font-semibold">
                            @if($totalProducts > 1)
                                Produits synchronisés
                            @elseif($totalProducts === 1)
                                1 produit synchronisé
                            @else
                                Aucun produit synchronisé
                            @endif
                        </span>
                        <span class="text-[11px] text-slate-400">
                            Données issues d’AbdouPharma
                        </span>
                    </div>
                </div>
            </div>

            {{-- Bouton Retour corrigé --}}
            <a href="/pharmacist/dashboard"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-300 bg-white shadow-sm text-sm font-medium text-slate-700 hover:text-emerald-700 hover:border-emerald-400 hover:bg-emerald-50/40 transition">
                <span class="text-lg">←</span>
                <span>Tableau de bord Pharmacien</span>
            </a>
        </div>

    </div>
@endsection

@section('content')

    {{-- Masquer la barre globale "PO / PHARMA-ONLINE / …" UNIQUEMENT sur cette page --}}
    <style>
        /* On cible simplement le tout premier <header> trouvé dans le body */
        body header:first-of-type {
            display: none !important;
        }
    </style>

    {{-- SI AUCUN PRODUIT --}}
    @if($products->count() === 0)
        <div class="p-8 bg-white rounded-2xl border border-dashed border-slate-300 text-center shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                ⚠️
            </div>
            <p class="text-slate-700 font-medium">Aucun produit synchronisé pour cette pharmacie.</p>
            <p class="text-slate-500 text-xs mt-1">
                Vérifiez la configuration de la connexion avec AbdouPharma ou relancez une synchronisation.
            </p>
        </div>

    @else

        {{-- TABLEAU PREMIUM --}}
        <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-200">

            {{-- Header tableau --}}
            <div class="bg-slate-900/90 px-4 py-4 flex items-center justify-between text-white text-xs md:text-sm">
                <span class="flex items-center gap-2">
                    <span class="h-2 w-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Inventaire synchronisé — {{ $pharmacyName }}
                </span>
                <span class="opacity-80 hidden sm:inline">
                    Libellé · Prix · Statut stock · Quantité · Dernière synchronisation
                </span>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto bg-white">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] tracking-wide">
                        <tr>
                            <th class="px-4 py-3 text-left">Produit</th>
                            <th class="px-4 py-3 text-right">Prix (FCFA)</th>
                            <th class="px-4 py-3 text-left">Stock</th>
                            <th class="px-4 py-3 text-center">Qté</th>
                            <th class="px-4 py-3 text-left">Synchronisé le</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @foreach($products as $p)
                            <tr class="hover:bg-slate-50 transition">

                                {{-- LIBELLE --}}
                                <td class="px-4 py-3 font-medium text-slate-800 max-w-xs">
                                    <div class="flex flex-col gap-0.5">
                                        <span>{{ $p->libelle }}</span>
                                        @if(!empty($p->code))
                                            <span class="text-[11px] text-slate-400">
                                                Code : {{ $p->code }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- PRIX --}}
                                <td class="px-4 py-3 text-right font-semibold text-slate-700 whitespace-nowrap">
                                    {{ number_format($p->prix, 0, ',', ' ') }} F
                                </td>

                                {{-- STOCK --}}
                                <td class="px-4 py-3">
                                    @if($p->stock === 'Disponible')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            ● Disponible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 border border-red-100">
                                            ● Rupture
                                        </span>
                                    @endif
                                </td>

                                {{-- QUANTITE --}}
                                <td class="px-4 py-3 text-center font-semibold text-slate-700">
                                    {{ $p->quantity }}
                                </td>

                                {{-- DATE --}}
                                <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $p->synced_at }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $products->links() }}
        </div>

    @endif

@endsection
