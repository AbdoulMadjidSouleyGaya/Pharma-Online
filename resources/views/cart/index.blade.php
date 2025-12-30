<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-green-700">Mon Panier</h2>
            <a href="{{ route('produits.commande') }}" class="text-sm text-blue-700 hover:underline">← Continuer mes achats</a>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-green-50 via-white to-blue-50">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

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

            @if(empty($cart))
                <div class="p-10 rounded-2xl bg-white border border-dashed border-green-300 text-center shadow-sm">
                    <div class="text-lg font-semibold text-gray-700 mb-1">Votre panier est vide</div>
                    <div class="text-sm text-gray-500">Ajoutez des produits disponibles depuis la page Produits & Commande.</div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-green-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-green-100/60 text-green-900">
                            <tr>
                                <th class="px-4 py-3">Produit</th>
                                <th class="px-4 py-3">Prix</th>
                                <th class="px-4 py-3">Qté</th>
                                <th class="px-4 py-3">Sous-total</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $item)
                                <tr class="border-t border-gray-100">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item['libelle'] }}</td>
                                    <td class="px-4 py-3">{{ number_format($item['prix'], 0, ',', ' ') }} F</td>
                                    <td class="px-4 py-3">{{ $item['qty'] }}</td>
                                    <td class="px-4 py-3">{{ number_format($item['prix'] * $item['qty'], 0, ',', ' ') }} F</td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('cart.remove') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                            <button class="px-3 py-2 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
                                                Retirer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right font-semibold">Total</td>
                                <td class="px-4 py-4 font-bold text-gray-900">{{ number_format($total, 0, ',', ' ') }} F</td>
                                <td class="px-4 py-4 text-right">
                                    <form method="POST" action="{{ route('cart.clear') }}">
                                        @csrf
                                        <button class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100">
                                            Vider le panier
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-6 text-right">
                    <button disabled class="px-5 py-3 rounded-xl bg-green-600 text-white shadow-md cursor-not-allowed">
                        Passer la commande (bientôt)
                    </button>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
