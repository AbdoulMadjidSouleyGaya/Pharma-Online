<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\CustomerOrder;
use App\Models\PharmaProduct;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Liste "Voir mes commandes" (fusion session + DB, normalisation)
     */
    public function index(Request $request)
    {
        // 1) Base : commandes en session (peuvent être vides ou incomplètes)
        $sessionOrders = session('orders', []);
        $sessionCol    = collect($sessionOrders);

        // Normalisation : tri ASC pour attribuer des numéros stables si manquants
        $normalized = $sessionCol
            ->sortBy(fn ($o) => $o['created_at'] ?? '')
            ->values()
            ->map(function ($o, $i) {
                $items       = is_array($o['items'] ?? null) ? $o['items'] : [];
                $o['number'] = $o['number'] ?? ($i + 1);
                $o['count']  = $o['count']  ?? count($items);
                $o['total']  = $o['total']  ?? collect($items)->sum('prix');

                // Statuts autorisés : en_attente | en_cours | valide | rejete | annulee
                $o['status'] = $o['status'] ?? 'en_attente';

                // Champs “traitement”
                $o['decision_comment'] = $o['decision_comment'] ?? null;
                $o['cashier_no']       = $o['cashier_no']       ?? null;
                $o['pharmacy_name']    = $o['pharmacy_name']    ?? null;

                return $o;
            });

        // Reconstituer par ID
        $byId = [];
        foreach ($normalized as $o) {
            if (!empty($o['id'])) {
                $byId[$o['id']] = $o;
            }
        }

        // 2) Overlay / Merge avec la DB (la DB fait foi)
        if (Auth::check()) {
            $dbRows = CustomerOrder::with('pharmacy')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($dbRows as $row) {
                $rid    = (string) $row->id;
                $fromDb = [
                    'id'               => $rid,
                    'number'           => (int) $row->number,
                    'items'            => is_array($row->items) ? $row->items : (array) $row->items,
                    'total'            => (int) ($row->total ?? 0),
                    'count'            => (int) ($row->count ?? 0),
                    'status'           => (string) ($row->status ?? 'en_attente'),
                    'created_at'       => optional($row->created_at)->toIso8601String(),
                    'decision_comment' => $row->decision_comment,
                    'cashier_no'       => $row->cashier_no,
                    'pharmacy_name'    => optional($row->pharmacy)->name,
                ];

                // Fusion (la DB écrase la session si conflit)
                $byId[$rid] = array_merge($byId[$rid] ?? [], $fromDb);
            }
        }

        // 3) Écrire la version fusionnée/normalisée en session
        session(['orders' => $byId]);

        // 4) Affichage : tri DESC par date (plus récentes en haut)
        $ordersOut = collect($byId)
            ->sortByDesc('created_at')
            ->values()
            ->all();

        return view('orders.index', [
            'orders' => $ordersOut,
            'count'  => count($ordersOut),
        ]);
    }

    /**
     * Crée une commande depuis la sélection de produits.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'pharmacy_id'     => ['required', 'exists:pharmacies,id'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.id'      => ['required', 'exists:pharma_products,id'],
            'items.*.qty'     => ['required', 'integer', 'min:1'],
            'items.*.price'   => ['nullable', 'numeric', 'min:0'],
        ]);

        $pharmacy = Pharmacy::findOrFail($data['pharmacy_id']);

        // Normalisation des items + calcul du total
        $normalizedItems = [];
        $total = 0;

        foreach ($data['items'] as $rawItem) {
            $id    = (int) $rawItem['id'];
            $qty   = (int) ($rawItem['qty'] ?? 1);
            $price = (int) ($rawItem['price'] ?? 0);

            $normalizedItems[] = [
                'id'    => $id,
                'qty'   => $qty,
                'price' => $price,
            ];

            $total += $price * $qty;
        }

        // Création de la commande
        $order = new CustomerOrder();
        $order->id          = (string) Str::uuid();
        $order->user_id     = $user->id;
        $order->pharmacy_id = $pharmacy->id;
        $order->status      = CustomerOrder::STATUS_EN_ATTENTE;
        $order->total       = $total;
        $order->count       = count($normalizedItems);
        $order->items       = $normalizedItems;

        // Génération d’un numéro lisible, séquentiel par pharmacie
        $order->number = (int) CustomerOrder::where('pharmacy_id', $pharmacy->id)->max('number') + 1;

        $order->save();

        Log::info('order.created', [
            'order_id'     => $order->id,
            'order_number' => $order->number,
            'pharmacy_id'  => $order->pharmacy_id,
            'user_id'      => $order->user_id,
            'items_count'  => $order->count,
            'total'        => $order->total,
        ]);

        event(new OrderCreated($order));

        return redirect()
            ->route('order.current')
            ->with('success', 'Votre commande a été enregistrée et sera traitée par la pharmacie.');
    }

    /**
     * Voir la dernière commande.
     */
    public function current(Request $request)
    {
        $last = session('last_order_id');

        if (!$last) {
            return redirect()
                ->route('orders.index')
                ->with('error', "Aucune commande récente à afficher.");
        }

        return redirect()->route('order.show', $last);
    }

    /**
     * Détail d’une commande (session d’abord, sinon DB + merge infos pharmacien).
     */
    public function show(Request $request, string $orderId)
    {
        // 1) Essayer depuis la session
        $order = data_get(session('orders', []), $orderId);

        // 2) Sinon, retomber sur la DB (et mettre la session à jour)
        if (!$order && Auth::check()) {
            $row = CustomerOrder::with('pharmacy')
                ->where('id', $orderId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$row) {
                abort(404);
            }

            $order = [
                'id'               => (string) $row->id,
                'number'           => (int) $row->number,
                'items'            => is_array($row->items) ? $row->items : (array) $row->items,
                'total'            => (int) ($row->total ?? 0),
                'count'            => (int) ($row->count ?? 0),
                'status'           => (string) ($row->status ?? 'en_attente'),
                'created_at'       => optional($row->created_at)->toIso8601String(),
                'decision_comment' => $row->decision_comment,
                'cashier_no'       => $row->cashier_no,
                'pharmacy_name'    => optional($row->pharmacy)->name,
            ];

            // Hydrate la session pour les prochains accès
            $orders = session('orders', []);
            $orders[$orderId] = $order;
            session(['orders' => $orders]);
        }

        // ✅ FIX ICI : hydrater les items (libelle/prix) pour la vue
        if (is_array($order)) {
            $order = $this->hydrateOrderItems($order);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Hydrate les items d'une commande avec les infos produit (libelle/prix).
     */
    private function hydrateOrderItems(array $order): array
    {
        $items = $order['items'] ?? [];
        if (!is_array($items) || empty($items)) {
            $order['items'] = [];
            return $order;
        }

        $ids = [];
        foreach ($items as $it) {
            $pid = (int) data_get($it, 'id', 0);
            if ($pid > 0) $ids[] = $pid;
        }
        $ids = array_values(array_unique($ids));

        $products = PharmaProduct::query()
            ->whereIn('id', $ids)
            ->get(['id', 'libelle', 'prix'])
            ->keyBy('id');

        $hydrated = [];
        foreach ($items as $it) {
            $pid = (int) data_get($it, 'id', 0);
            $qty = (int) data_get($it, 'qty', 0);

            $unit = data_get($it, 'price', null);
            $unit = is_numeric($unit) ? (int) $unit : null;

            $p = $pid > 0 ? $products->get($pid) : null;
            $libelle = $p?->libelle ?? (string) data_get($it, 'libelle', '—');

            $prix = $unit ?? (is_numeric($p?->prix) ? (int) $p->prix : 0);

            $hydrated[] = [
                'id'         => $pid,
                'qty'        => $qty,
                'price'      => $prix,
                'libelle'    => $libelle,
                'prix'       => $prix,
                'line_total' => (int) ($prix * max(1, $qty)),
            ];
        }

        $order['items'] = $hydrated;

        if (!isset($order['count']) || (int)$order['count'] <= 0) {
            $order['count'] = count($hydrated);
        }

        return $order;
    }

    // ... le reste de ton controller ne change pas (cancel, setStatus, destroy)
    public function cancel(Request $request, string $orderId)
    {
        $orders = session('orders', []);
        $order  = $orders[$orderId] ?? null;

        if (!$order) {
            $row = CustomerOrder::with('pharmacy')
                ->where('id', $orderId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$row) {
                return back()->with('error', "Commande introuvable.");
            }

            $order = [
                'id'               => (string) $row->id,
                'number'           => (int) $row->number,
                'items'            => is_array($row->items) ? $row->items : (array) $row->items,
                'total'            => (int) ($row->total ?? 0),
                'count'            => (int) ($row->count ?? 0),
                'status'           => (string) ($row->status ?? 'en_attente'),
                'created_at'       => optional($row->created_at)->toIso8601String(),
                'decision_comment' => $row->decision_comment,
                'cashier_no'       => $row->cashier_no,
                'pharmacy_name'    => optional($row->pharmacy)->name ?? null,
            ];
        }

        if (($order['status'] ?? 'en_attente') === 'valide') {
            return back()->with('error', "Cette commande est validée et ne peut pas être annulée.");
        }

        CustomerOrder::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->update(['status' => 'annulee']);

        $order['status']      = 'annulee';
        $orders[$orderId]     = $order;
        session(['orders' => $orders]);

        return back()->with('success', "Commande N°" . ($order['number'] ?? '') . " annulée.");
    }

    public function setStatus(Request $request, string $orderId)
    {
        $data = $request->validate([
            'status' => ['required', 'in:en_attente,en_cours,valide,rejete,annulee'],
        ]);

        $orders = session('orders', []);

        if (!isset($orders[$orderId])) {
            $row = CustomerOrder::with('pharmacy')
                ->where('id', $orderId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$row) {
                return back()->with('error', "Commande introuvable.");
            }

            $orders[$orderId] = [
                'id'               => (string) $row->id,
                'number'           => (int) $row->number,
                'items'            => is_array($row->items) ? $row->items : (array) $row->items,
                'total'            => (int) ($row->total ?? 0),
                'count'            => (int) ($row->count ?? 0),
                'status'           => (string) ($row->status ?? 'en_attente'),
                'created_at'       => optional($row->created_at)->toIso8601String(),
                'decision_comment' => $row->decision_comment,
                'cashier_no'       => $row->cashier_no,
                'pharmacy_name'    => optional($row->pharmacy)->name ?? null,
            ];
        }

        if (($orders[$orderId]['status'] ?? null) === 'valide' && $data['status'] !== 'valide') {
            return back()->with('error', "Commande déjà validée, statut non modifiable.");
        }

        CustomerOrder::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->update(['status' => $data['status']]);

        $orders[$orderId]['status'] = $data['status'];
        session(['orders' => $orders]);

        return back()->with('success', "Statut mis à jour.");
    }

    public function destroy(Request $request, string $orderId)
    {
        $orders = session('orders', []);
        $order  = $orders[$orderId] ?? null;

        if (!$order) {
            $row = CustomerOrder::with('pharmacy')
                ->where('id', $orderId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$row) {
                return back()->with('error', "Commande introuvable.");
            }

            $order = [
                'id'               => (string) $row->id,
                'number'           => (int) $row->number,
                'items'            => is_array($row->items) ? $row->items : (array) $row->items,
                'total'            => (int) ($row->total ?? 0),
                'count'            => (int) ($row->count ?? 0),
                'status'           => (string) ($row->status ?? 'en_attente'),
                'created_at'       => optional($row->created_at)->toIso8601String(),
                'decision_comment' => $row->decision_comment,
                'cashier_no'       => $row->cashier_no,
                'pharmacy_name'    => optional($row->pharmacy)->name ?? null,
            ];
        }

        if (($order['status'] ?? 'en_attente') !== 'annulee') {
            return back()->with('error', "Seules les commandes annulées peuvent être supprimées.");
        }

        CustomerOrder::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'annulee')
            ->delete();

        unset($orders[$orderId]);
        if (session('last_order_id') === $orderId) {
            session()->forget('last_order_id');
        }
        session(['orders' => $orders]);

        return redirect()
            ->route('orders.index')
            ->with('success', "Commande N°" . ($order['number'] ?? '') . " supprimée.");
    }
}
