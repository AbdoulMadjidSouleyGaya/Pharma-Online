<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-green-700">Détails de la commande</h2>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-green-50 via-white to-blue-50">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

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

            @php
                $id        = $order['id'] ?? '';
                $created   = $order['created_at'] ?? null;
                $count     = $order['count'] ?? (is_countable($order['items'] ?? null) ? count($order['items']) : 0);
                $total     = (int) ($order['total'] ?? 0);
                $status    = $order['status'] ?? 'en_attente';
                $number    = $order['number'] ?? null;

                $map = [
                    'en_attente' => ['label' => 'En attente', 'class' => 'bg-yellow-100 text-yellow-800 border border-yellow-200'],
                    'en_cours'   => ['label' => 'En cours',   'class' => 'bg-blue-100 text-blue-800 border border-blue-200'],
                    'valide'     => ['label' => 'Validée',    'class' => 'bg-green-100 text-green-800 border border-green-200'],
                    'rejete'     => ['label' => 'Rejetée',    'class' => 'bg-red-100 text-red-800 border border-red-200'],
                    'annulee'    => ['label' => 'Annulée',    'class' => 'bg-gray-100 text-gray-800 border border-gray-200'],
                ];
                $st = $map[$status] ?? $map['en_attente'];
            @endphp

            {{-- En-tête commande --}}
            <div class="bg-white rounded-2xl border border-green-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-green-50 via-white to-blue-50">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="text-sm text-gray-500">Référence</div>
                            <div class="font-mono text-gray-800">{{ $id }}</div>
                        </div>
                        <div class="hidden sm:block h-10 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-sm text-gray-500">Commande</div>
                            <div class="font-semibold text-gray-800">N°{{ $number ?? '—' }}</div>
                        </div>
                        <div class="hidden sm:block h-10 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-sm text-gray-500">Date</div>
                            <div class="text-gray-800">
                                @if($created)
                                    {{ \Carbon\Carbon::parse($created)->locale('fr')->isoFormat('LLLL') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="hidden sm:block h-10 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-sm text-gray-500">Statut</div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $st['class'] }}">
                                {{ $st['label'] }}
                            </span>
                        </div>
                        <div class="hidden sm:block h-10 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-sm text-gray-500">Articles</div>
                            <div class="font-semibold text-gray-800">{{ $count }}</div>
                        </div>
                        <div class="hidden sm:block h-10 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-sm text-gray-500">Total</div>
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($total, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                </div>

                {{-- Tableau produits --}}
                <div class="px-6 py-4">
                    @if(!empty($order['items']))
                        <table class="w-full">
                            <thead class="bg-green-100/60 text-green-900">
                                <tr>
                                    <th class="px-3 py-2 text-left">Produit</th>
                                    <th class="px-3 py-2 text-left w-40">Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order['items'] as $item)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-3 text-gray-800">
                                            {{ $item['libelle'] ?? '—' }}
                                        </td>
                                        <td class="px-3 py-3">
                                            {{ isset($item['prix']) ? number_format((float)$item['prix'], 0, ',', ' ') . ' F' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td class="px-3 py-3 text-right font-semibold">Total</td>
                                    <td class="px-3 py-3 font-bold text-gray-900">
                                        {{ number_format($total, 0, ',', ' ') }} F
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="p-8 text-center text-gray-500">
                            Aucun article dans cette commande.
                        </div>
                    @endif

                    {{-- Bouton retour : uniquement vers la liste des commandes --}}
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('orders.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50">
                            ← Retour à la page des commandes
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>