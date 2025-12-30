<x-app-layout>
    @php
        // Sécurisation + récupération de la pharmacie liée aux commandes
        $orders = $orders ?? [];
        $firstPharmacyId = null;

        if (!empty($orders)) {
            // Si c'est une collection, on prend le premier élément
            if ($orders instanceof \Illuminate\Support\Collection) {
                $first = $orders->first();
            } else {
                // Tableau classique
                $first = reset($orders);
            }

            if (is_array($first)) {
                $firstPharmacyId = $first['pharmacy_id'] ?? null;
            } elseif (is_object($first)) {
                $firstPharmacyId = $first->pharmacy_id ?? null;
            }
        }
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-green-700">Voir mes commandes</h2>

            @if($firstPharmacyId)
                {{-- 🔁 Retour vers la dernière pharmacie utilisée --}}
                <a href="{{ route('produits.commande.pharmacy', ['pharmacy' => $firstPharmacyId]) }}"
                   class="text-sm text-blue-700 hover:underline">
                    ← Continuer mes achats
                </a>
            @else
                {{-- 🏥 Fallback : liste des pharmacies si on ne connaît pas la pharmacie --}}
                <a href="{{ route('produits.commande.pharmacies') }}"
                   class="text-sm text-blue-700 hover:underline">
                    ← Continuer mes achats
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-green-50 via-white to-blue-50">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- ✅ Messages flash --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2">
                    {{ session('success') }}
                </div>
            @endif

            @php
                // $orders déjà normalisé plus haut
            @endphp

            {{-- ✅ Si aucune commande --}}
            @if(empty($orders))
                <div class="p-10 rounded-2xl bg-white border border-dashed border-green-300 text-center shadow-sm">
                    <div class="text-lg font-semibold text-gray-700 mb-1">Aucune commande pour le moment</div>
                    <div class="text-sm text-gray-500">Sélectionnez des produits puis envoyez votre commande.</div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-green-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-green-100/60 text-green-900">
                            <tr>
                                <th class="px-4 py-3">Commande</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Produits</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3">Actions</th>
                                <th class="px-4 py-3">Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                @php
                                    $orderId     = $order['id']         ?? null;
                                    $number      = $order['number']     ?? $loop->iteration;
                                    $createdRaw  = $order['created_at'] ?? null;
                                    $count       = $order['count'] ?? (is_countable($order['items'] ?? null) ? count($order['items']) : 0);
                                    $total       = (int) ($order['total'] ?? 0);

                                    $status = $order['status'] ?? 'en_attente';
                                    $map = [
                                        'en_attente' => ['label' => 'En attente', 'class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200'],
                                        'en_cours'   => ['label' => 'En cours',   'class' => 'bg-blue-100 text-blue-800 border border-blue-200'],
                                        'valide'     => ['label' => 'Validée',    'class' => 'bg-green-100 text-green-800 border border-green-200'],
                                        'rejete'     => ['label' => 'Rejetée',    'class' => 'bg-red-100 text-red-800 border border-red-200'],
                                        'annulee'    => ['label' => 'Annulée',    'class' => 'bg-gray-100 text-gray-800 border border-gray-200'],
                                    ];
                                    $st = $map[$status] ?? $map['en_attente'];

                                    $locked    = ($status === 'valide');     // bloque modifier/annuler
                                    $deletable = ($status === 'annulee');    // suppression autorisée seulement si "annulee"
                                @endphp

                                <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 font-semibold text-gray-800">
                                        Commande N°{{ $number }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        @if($createdRaw)
                                            {{ \Carbon\Carbon::parse($createdRaw)->locale('fr')->isoFormat('LLL') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $count }}</td>
                                    <td class="px-4 py-3 font-medium">{{ number_format($total, 0, ',', ' ') }} F</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $st['class'] }}">
                                            {{ $st['label'] }}
                                        </span>
                                    </td>

                                    {{-- ✅ Colonne Actions (Détails + Modifier + Annuler) --}}
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            {{-- Bouton Détails --}}
                                            @if($orderId)
                                                <a href="{{ route('order.show', $orderId) }}"
                                                   class="px-3 py-2 rounded-lg border border-green-200 text-green-700 hover:bg-green-50">
                                                    Détails
                                                </a>
                                            @else
                                                <span class="px-3 py-2 rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed">
                                                    Détails
                                                </span>
                                            @endif

                                            {{-- Bouton Modifier --}}
                                            @if($orderId)
                                                <a href="{{ route('order.show', $orderId) }}"
                                                   class="px-3 py-2 rounded-lg border {{ $locked ? 'cursor-not-allowed opacity-50 border-gray-200 text-gray-400 pointer-events-none' : 'border-blue-200 text-blue-700 hover:bg-blue-50' }}">
                                                    Modifier
                                                </a>
                                            @else
                                                <span class="px-3 py-2 rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed">
                                                    Modifier
                                                </span>
                                            @endif

                                            {{-- Bouton Annuler --}}
                                            @if($orderId)
                                                <form method="POST" action="{{ route('order.cancel', $orderId) }}"
                                                      onsubmit="return confirm('Annuler cette commande ?');">
                                                    @csrf
                                                    <button
                                                        class="px-3 py-2 rounded-lg border {{ $locked ? 'cursor-not-allowed opacity-50 border-gray-200 text-gray-400' : 'border-red-200 text-red-700 hover:bg-red-50' }}"
                                                        {{ $locked ? 'disabled' : '' }}>
                                                        Annuler
                                                    </button>
                                                </form>
                                            @else
                                                <button class="px-3 py-2 rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed" disabled>
                                                    Annuler
                                                </button>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- ✅ Colonne Supprimer (si Annulée) --}}
                                    <td class="px-4 py-3">
                                        @if($orderId)
                                            <form method="POST" action="{{ route('order.destroy', $orderId) }}"
                                                  onsubmit="return confirm('Supprimer définitivement cette commande ?');">
                                                @csrf
                                                <button
                                                    class="px-3 py-2 rounded-lg border {{ $deletable ? 'border-gray-300 text-gray-700 hover:bg-gray-100' : 'cursor-not-allowed opacity-50 border-gray-200 text-gray-400' }}"
                                                    {{ $deletable ? '' : 'disabled' }}>
                                                    Supprimer
                                                </button>
                                            </form>
                                        @else
                                            <button class="px-3 py-2 rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed" disabled>
                                                Supprimer
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
