<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 font-bold text-2xl text-green-700 tracking-wide">
                Produits &amp; Commande
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-blue-700 hover:text-blue-800 hover:underline">
                ← Retour au tableau de bord
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-green-100 via-white to-blue-100 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Barre de recherche --}}
            <div class="bg-white border border-green-200 shadow-lg rounded-2xl p-6 mb-10">
                <form method="GET" action="{{ route('produits.commande') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" name="q" value="{{ $params['q'] ?? '' }}"
                               class="w-full rounded-xl border border-green-300 focus:ring-2 focus:ring-green-400 focus:border-green-400 focus:outline-none
                                      px-5 py-3 placeholder-gray-400 text-gray-800 transition"
                               placeholder="🔍 Rechercher un produit (ex : Paracétamol)">
                    </div>
                    <button
                        class="md:w-48 w-full inline-flex items-center justify-center px-6 py-3
                               bg-gradient-to-r from-green-600 to-blue-600 text-white font-semibold rounded-xl
                               shadow-md hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200">
                        Rechercher
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">
                    Cochez les produits disponibles que vous souhaitez commander.
                </p>
            </div>

            {{-- Messages flash --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Grille des produits --}}
            @if($products->count())
                <form id="order-form" method="POST" action="{{ route('order.store') }}">
                    @csrf
                    {{-- les inputs "product_ids[]" seront injectés en JS --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $p)
                            @php
                                $stockNorm = strtolower(trim((string) $p->stock));
                                $isDisponible = in_array($stockNorm, ['disponible','en stock','stocké']);
                                $price = is_null($p->prix) ? 0 : (float)$p->prix;
                            @endphp

                            <div class="group bg-white rounded-3xl border border-green-100 shadow-sm hover:shadow-2xl transition duration-200 overflow-hidden relative"
                                 data-product
                                 data-id="{{ $p->id }}"
                                 data-price="{{ $price }}"
                                 data-name="{{ $p->libelle }}"
                            >
                                <div class="h-1.5 w-full bg-gradient-to-r from-green-400 via-blue-400 to-green-400"></div>

                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <h3 class="font-bold text-gray-800 text-lg group-hover:text-green-700"
                                            style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                            {{ $p->libelle }}
                                        </h3>

                                        {{-- Case à cocher (uniquement si disponible) --}}
                                        @if($isDisponible)
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                       class="product-select h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500"
                                                       value="{{ $p->id }}">
                                            </label>
                                        @endif
                                    </div>

                                    {{-- Prix --}}
                                    <p class="mt-3 text-sm text-gray-700">
                                        @if(!is_null($p->prix))
                                            <span class="font-medium text-green-700">Prix :</span>
                                            {{ number_format($price, 0, ',', ' ') }} F
                                        @else
                                            <span class="text-gray-400 italic">Prix non renseigné</span>
                                        @endif
                                    </p>

                                    {{-- Statut --}}
                                    <div class="mt-2">
                                        @if($isDisponible)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                                         bg-green-200 text-green-800 border border-green-300">
                                                Disponible
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                                         bg-red-200 text-red-800 border border-red-300">
                                                Rupture
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="absolute -right-10 -top-10 h-24 w-24 rounded-full bg-green-100 opacity-30"></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>

                    {{-- Panneau récap "collant" --}}
                    <div id="order-summary"
                         class="fixed bottom-4 right-4 z-40 w-full max-w-md
                                sm:bottom-6 sm:right-6
                                bg-white border border-green-200 shadow-2xl rounded-2xl p-4
                                hidden sm:block">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-500">Produits sélectionnés</div>
                                <div class="text-2xl font-bold text-gray-900"><span id="sel-count">0</span></div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Total estimé</div>
                                <div class="text-2xl font-bold text-gray-900"><span id="sel-total">0</span> F</div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-3">
                            <button type="submit"
                                id="btn-send"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-green-600 text-white font-semibold shadow hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed"
                                disabled>
                                Envoyer ma commande
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Seuls les produits <strong>disponibles</strong> peuvent être sélectionnés.
                        </p>
                    </div>

                    {{-- Variante mobile : panneau sous la liste --}}
                    <div id="order-summary-mobile" class="sm:hidden mt-6 bg-white border border-green-200 shadow rounded-2xl p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-500">Sélection</div>
                                <div class="text-xl font-bold text-gray-900"><span id="sel-count-m">0</span></div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Total</div>
                                <div class="text-xl font-bold text-gray-900"><span id="sel-total-m">0</span> F</div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-3">
                            <button type="submit"
                                id="btn-send-m"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-green-600 text-white font-semibold shadow hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed"
                                disabled>
                                Envoyer
                            </button>
                            <a href="{{ route('order.current') }}"
                               class="px-4 py-2.5 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50">
                                Voir
                            </a>
                        </div>
                    </div>
                </form>
            @else
                <div class="p-10 rounded-3xl bg-white border border-dashed border-green-300 text-center shadow-sm">
                    <div class="text-lg font-semibold text-gray-700 mb-1">Aucun produit trouvé</div>
                    <div class="text-sm text-gray-500">Essaye un autre mot-clé, ou vide la recherche.</div>
                </div>
            @endif
        </div>
    </div>

    {{-- JS léger : gestion sélection + total + injection inputs --}}
    <script>
        (function () {
            const formatter = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 });

            const checkboxes = document.querySelectorAll('.product-select');
            const form       = document.getElementById('order-form');

            const selCount   = document.getElementById('sel-count');
            const selTotal   = document.getElementById('sel-total');
            const btnSend    = document.getElementById('btn-send');

            const selCountM  = document.getElementById('sel-count-m');
            const selTotalM  = document.getElementById('sel-total-m');
            const btnSendM   = document.getElementById('btn-send-m');

            function updateSummary() {
                let ids = [];
                let total = 0;

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        ids.push(cb.value);
                        const card = cb.closest('[data-product]');
                        const price = Number(card?.dataset?.price || 0);
                        total += price;
                    }
                });

                // Affichage
                const count = ids.length;
                [selCount, selCountM].forEach(el => el && (el.textContent = count));
                [selTotal, selTotalM].forEach(el => el && (el.textContent = formatter.format(total)));

                // Activer/désactiver le bouton
                const enabled = count > 0;
                [btnSend, btnSendM].forEach(b => b && (b.disabled = !enabled));

                // Nettoyer les inputs existants
                form.querySelectorAll('input[name="product_ids[]"]').forEach(n => n.remove());
                // Injecter les inputs pour l'envoi POST
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'product_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSummary);
            });

            // init au chargement (utile après back() navigateur)
            updateSummary();
        })();
    </script>
</x-app-layout>
