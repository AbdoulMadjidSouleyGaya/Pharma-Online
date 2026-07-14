<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Pharmacy;
use App\Models\PharmaProduct;
use App\Models\UserNotification;
use App\Services\RemotePharmacySync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Events\OrderCreated;

class PharmacistOrderController extends Controller
{
    /**
     * Récupère la pharmacie du pharmacien connecté.
     */
    protected function currentPharmacy(): ?Pharmacy
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        if (!empty($user->pharmacy_id)) {
            return Pharmacy::find($user->pharmacy_id);
        }

        if (method_exists($user, 'pharmacies')) {
            return $user->pharmacies()->first();
        }

        return null;
    }

    /**
 * Retourne le nombre de commandes en attente pour la pharmacie du pharmacien connecté.
 * Réponse JSON : { pending: <int> }
 */
public function pendingCount()
{
    $pharmacy = $this->currentPharmacy();

    if (! $pharmacy) {
        return response()->json([
            'pending' => 0,
        ]);
    }

    // ⚠ On couvre tous les statuts "en attente" possibles dans ton projet
    $pendingStatuses = ['en_attente', 'en_cours', 'pending'];

    $pending = CustomerOrder::query()
        ->where('pharmacy_id', $pharmacy->id)
        ->whereIn('status', $pendingStatuses)
        ->count();

    return response()->json([
        'pending' => $pending,
    ]);
}


    /**
     * Liste des commandes de la pharmacie du pharmacien.
     * URL : /pharmacist/orders?status=pending|validated|rejected|...
     */
    public function index(Request $request)
    {
        $pharmacy = $this->currentPharmacy();
        abort_unless($pharmacy, 403, "Aucune pharmacie rattachée.");

        // Filtre dans l’URL : pending (par défaut), validated, rejected...
        $filter = $request->query('status', CustomerOrder::FILTER_PENDING);

        $query = CustomerOrder::with(['user', 'pharmacy'])
            ->forPharmacyAndStatusFilter($pharmacy->id, $filter);

        $orders = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Petits compteurs pour la vue (utilise la même logique centrale)
        $counts = CustomerOrder::countsByFilterForPharmacy($pharmacy->id);

        return view('pharmacist.orders.index', [
            'orders'   => $orders,
            'pharmacy' => $pharmacy,
            'counts'   => $counts,
            'filter'   => $filter,                                   // ex. "pending"
            'status'   => CustomerOrder::mapFilterToStatus($filter), // ex. "en_attente"
        ]);
    }

    /**
     * Création d’une commande (ancienne version côté client).
     * (Tu utilises maintenant OrderController@store, on la laisse pour compatibilité.)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pharmacy_id'   => ['required', 'integer', 'exists:pharmacies,id'],
            'product_ids'   => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'distinct', 'exists:pharma_products,id'],
        ]);

        // 🔹 Pharmacie choisie par l'utilisateur
        $pharmacy = Pharmacy::findOrFail($data['pharmacy_id']);

        // 🔹 On ne prend que les produits de CETTE pharmacie
        $products = PharmaProduct::where('pharmacy_id', $pharmacy->id)
            ->whereIn('id', $data['product_ids'])
            ->get();

        $items = [];
        $total = 0;
        foreach ($products as $p) {
            $stock = strtolower(trim((string) $p->stock));
            $isDisponible = in_array($stock, ['disponible', 'en stock', 'stocké']);
            if (!$isDisponible) {
                continue;
            }

            $price = is_null($p->prix) ? 0 : (float) $p->prix;
            $items[] = [
                'id'      => $p->id,
                'libelle' => $p->libelle,
                'prix'    => $price,
            ];
            $total += $price;
        }

        if (empty($items)) {
            return back()->with('error', "Aucun produit disponible n'a été sélectionné.");
        }

        // ID commande + numéro lisible
        $orderId    = (string) Str::uuid();
        $nextNumber = (int) (CustomerOrder::where('user_id', Auth::id())->max('number') ?? 0) + 1;
        $prescriptionPath = $request->session()->get('prescription_path');
        $prescriptionOriginalName = $request->session()->get('prescription_original');

        // 🔹 1) Sauvegarde DB avec LA PHARMACIE CHOISIE
        // Assignation explicite (pas de mass assignment) : total/status/pharmacy_id
        // sont des valeurs calculées côté serveur, jamais reprises du client.
        $created = new CustomerOrder();
        $created->id          = $orderId;
        $created->user_id     = Auth::id();
        $created->number      = $nextNumber;
        $created->count       = count($items);
        $created->total       = (int) $total;
        $created->status      = 'en_attente';   // en_attente | en_cours | valide | rejete | annulee
        $created->items       = $items;         // cast JSON dans le modèle
        $created->pharmacy_id = $pharmacy->id;  // ✅ TRÈS IMPORTANT
        $created->prescription_path = $prescriptionPath;
        $created->prescription_original_name = $prescriptionOriginalName;
        $created->save();

        // 🔹 1bis) Diffusion temps réel
        event(new OrderCreated($created));

        // 🔹 2) Session pour affichage côté client
        $orders = session('orders', []);
        $orders[$orderId] = [
            'id'               => $orderId,
            'number'           => $nextNumber,
            'items'            => $items,
            'total'            => $total,
            'count'            => count($items),
            'status'           => 'en_attente',
            'created_at'       => now()->toIso8601String(),
            'decision_comment' => null,
            'cashier_no'       => null,
            'pharmacy_name'    => $pharmacy->name, // ✅ nom affiché côté client
        ];
        session(['orders' => $orders, 'last_order_id' => $orderId]);

        return redirect()
            ->route('orders.index')
            ->with('success', '✅ Votre commande a bien été envoyée.');
    }

    /**
     * Détail d’une commande (côté pharmacien).
     */
    public function show(CustomerOrder $order)
    {
        $user     = auth()->user();
        $pharmacy = $this->currentPharmacy();
        abort_unless($pharmacy, 403, "Aucune pharmacie rattachée.");

        if (! is_null($order->pharmacy_id) && (int) $order->pharmacy_id !== (int) $pharmacy->id) {
            abort(403, "Cette commande n’appartient pas à votre pharmacie.");
        }

        // On récupère les items bruts (id, qty, price, libelle, prix, …)
        $rawItems = collect($order->items ?? []);

        // On récupère tous les IDs produits concernés
        $productIds = $rawItems
            ->pluck('id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->all();

        // On charge les produits de cette pharmacie pour ces IDs
        $products = PharmaProduct::where('pharmacy_id', $pharmacy->id ?? null)
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        // On construit une liste d’items enrichis pour l’affichage
        $items = $rawItems->map(function ($item) use ($products) {
            // $item peut être array ou object
            if (is_object($item)) {
                $item = (array) $item;
            }

            $productId = (int) ($item['id'] ?? 0);
            $qty       = (int) ($item['qty'] ?? ($item['quantity'] ?? 1));

            $product   = $products[$productId] ?? null;

            // Nom : priorité au produit, sinon au libellé de l’item, sinon fallback
            $name = $product?->libelle
                ?? ($item['libelle'] ?? ('Produit #' . $productId));

            // Prix unitaire : plusieurs cas possibles (nouveau & ancien format)
            if (isset($item['price'])) {
                $unitPrice = (int) $item['price'];
            } elseif (isset($item['prix'])) {
                $unitPrice = (int) $item['prix'];
            } else {
                $unitPrice = (int) ($product->prix ?? 0);
            }

            $lineTotal = $unitPrice * max(1, $qty);

            return [
                'product_id' => $productId,
                'name'       => $name,
                'qty'        => max(1, $qty),
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        });

        return view('pharmacist.orders.show', [
            'order'    => $order,
            'pharmacy' => $pharmacy,
            'items'    => $items,
            'user'     => $user,
        ]);
    }

    /**
     * Affiche l'ordonnance téléversée dans la commande.
     *
     * Le fichier est stocké sur le disque privé "local" (storage/app/private,
     * hors du lien symbolique public/storage) : il n'est jamais accessible en
     * direct par le serveur web, uniquement via cette route après vérification
     * que la commande appartient bien à la pharmacie connectée.
     */
    public function prescription(CustomerOrder $order)
    {
        $pharmacy = $this->currentPharmacy();
        abort_unless($pharmacy, 403, "Aucune pharmacie rattachée.");

        if (! is_null($order->pharmacy_id) && (int) $order->pharmacy_id !== (int) $pharmacy->id) {
            abort(403, "Cette commande n’appartient pas à votre pharmacie.");
        }

        $path = trim((string) $order->prescription_path);
        abort_if($path === '', 404, "Aucune ordonnance n’est rattachée à cette commande.");

        $path = ltrim(str_replace('\\', '/', $path), '/');
        abort_if(str_contains($path, '..') || str_starts_with($path, 'storage/'), 404);
        abort_unless(Storage::disk('local')->exists($path), 404, "Le fichier de l’ordonnance est introuvable.");

        $absolutePath = Storage::disk('local')->path($path);
        $downloadName = $order->prescription_original_name ?: basename($path);
        $mimeType = Storage::disk('local')->mimeType($path) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.addslashes($downloadName).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Valider / Rejeter + décrémentation.
     */
    public function updateStatus(
        Request $request,
        string $orderId,
        RemotePharmacySync $remoteSync
    ) {
        $data = $request->validate([
            'decision'           => ['required','in:valide,rejete'],
            'comment'            => ['nullable','string','max:2000'],
            'with_prescription'  => ['nullable','boolean'],
            'cashier_no'         => ['nullable','integer','between:1,5'],
        ]);

        $pharmacy = $this->currentPharmacy();
        abort_unless($pharmacy, 403, "Aucune pharmacie rattachée.");

        $order = CustomerOrder::with(['user','pharmacy'])->findOrFail($orderId);

        // sécurité d’accès
        if (!is_null($order->pharmacy_id) && (int) $order->pharmacy_id !== (int) $pharmacy->id) {
            abort(403, "Cette commande n’appartient pas à votre pharmacie.");
        }

        // affectation auto
        if (is_null($order->pharmacy_id)) {
            $order->pharmacy_id = $pharmacy->id;
        }

        // si déjà validée
        if ($order->status === 'valide' && $data['decision'] !== 'valide') {
            return back()->with('error', "Cette commande a déjà été validée.");
        }

        $pharmacyName = $pharmacy->name ?? 'Pharmacie partenaire PharmaOnline';

        /* =========================
         *  CHEMIN VALIDER
         * ========================= */
        if ($data['decision'] === 'valide') {

            // caisse obligatoire
            if (empty($data['cashier_no'])) {
                return back()
                    ->withInput()
                    ->with('error', "Veuillez sélectionner une caisse (1 à 5) avant de valider la commande.");
            }

            $clientName   = optional($order->user)->name ?: 'Client';
            $ordPhrase    = !empty($data['with_prescription']) ? " muni de votre ordonnance" : "";
            $comment = "Bonjour {$clientName}, Votre commande a été validée par {$pharmacyName}. "
                     . "Veuillez vous rendre à la caisse N°{$data['cashier_no']} pour le paiement{$ordPhrase}. "
                     . "Merci de vous présenter pour le retrait. Cordialement, L’équipe PharmaOnline";

            // maj commande
            $order->status = 'valide';
            $order->decision_comment = $comment;
            $order->cashier_no = (int) $data['cashier_no'];
            $order->save();

            // 🔻 décrémentation (local + AbdouPharma)
            $this->decrementProductsFromOrder($order, $pharmacy, $remoteSync);

            // notif interne
            if ($order->user_id) {
                UserNotification::create([
                    'user_id' => $order->user_id,
                    'title'   => "Votre commande #{$order->number} a été validée",
                    'body'    => $comment,
                ]);
            }

            // email
            if ($order->user && $order->user->email) {
                $subject = "PharmaOnline - Commande #{$order->number} validée par {$pharmacyName} ✅";
                Mail::raw($comment, function ($mail) use ($order, $subject) {
                    $mail->to($order->user->email)
                        ->subject($subject)
                        ->from('no-reply@pharmaonline.test', 'PharmaOnline');
                });
            }

            return redirect()
                ->route('pharmacist.orders.index', ['status' => 'validated'])
                ->with('success', "Commande #{$order->number} validée (caisse N°{$order->cashier_no}) et notification envoyée.");
        }

        /* =========================
         *  CHEMIN REJETER
         * ========================= */
        if (!is_null($order->cashier_no)) {
            return back()->with('error', "Caisse déjà assignée : cette commande ne peut plus être rejetée.");
        }

        if (!isset($data['comment']) || trim($data['comment']) === '') {
            return back()
                ->withInput()
                ->with('error', "Veuillez renseigner le motif du rejet.")
                ->withErrors(['comment' => 'Le motif du rejet est obligatoire.']);
        }

        $order->status = 'rejete';
        $order->decision_comment = trim($data['comment']);
        $order->save();

        if ($order->user_id) {
            $title = "Votre commande #{$order->number} a été rejetée par {$pharmacyName}";
            $body  = "Votre commande a été rejetée par {$pharmacyName}. Motif : {$order->decision_comment}";
            UserNotification::create([
                'user_id' => $order->user_id,
                'title'   => $title,
                'body'    => $body,
            ]);
        }

        if ($order->user && $order->user->email) {
            $subject = "PharmaOnline - Commande #{$order->number} rejetée par {$pharmacyName} ❌";
            $body = "Bonjour {$order->user->name}, Votre commande a été rejetée par {$pharmacyName}. "
                  . "Motif : {$order->decision_comment}. Cordialement, L’équipe PharmaOnline";

            Mail::raw($body, function ($mail) use ($order, $subject) {
                $mail->to($order->user->email)
                    ->subject($subject)
                    ->from('no-reply@pharmaonline.test', 'PharmaOnline');
            });
        }

        return redirect()
            ->route('pharmacist.orders.index', ['status' => 'rejected'])
            ->with('success', "Commande #{$order->number} rejetée et notification envoyée.");
    }
        /**
     * Décrémente les produits liés à la commande.
     * On essaie plusieurs colonnes possibles (JSON ou array) + on décode si string.
     */
    protected function decrementProductsFromOrder(
    CustomerOrder $order,
    Pharmacy $pharmacy,
    RemotePharmacySync $remoteSync
): void {
    $lines = $this->extractOrderLines($order);

    if (empty($lines)) {
        Log::info('⚠️ Aucune ligne de commande trouvée.', [
            'order_id'    => $order->id,
            'pharmacy_id' => $pharmacy->id,
        ]);
        return;
    }

    foreach ($lines as $line) {
        // Valeurs par défaut
        $orderedQty = 1;
        $remoteId   = null;
        $localId    = null;

        if (is_array($line)) {
            $orderedQty = (int)($line['quantity'] ?? $line['qty'] ?? 1);
            $remoteId   = $line['remote_id'] ?? null;
            $localId    = $line['id'] ?? $line['pharma_product_id'] ?? $line['product_id'] ?? null;
        } elseif (is_object($line)) {
            $orderedQty = (int)($line->quantity ?? $line->qty ?? 1);
            $remoteId   = $line->remote_id ?? null;
            $localId    = $line->id ?? $line->pharma_product_id ?? $line->product_id ?? null;
        }

        // Log pour voir ce qu’il traite
        Log::info('📦 Produit détecté pour décrémentation', [
            'order_id'    => $order->id,
            'pharmacy_id' => $pharmacy->id,
            'local_id'    => $localId,
            'remote_id'   => $remoteId,
            'ordered_qty' => $orderedQty,
        ]);

        // Décrémentation locale, verrouillée en base pour éviter toute
        // survente si deux commandes sur le même produit sont traitées
        // en même temps (lecture → soustraction → écriture non atomique).
        $product = null;
        $oldQty  = null;
        $newQty  = null;

        DB::transaction(function () use ($pharmacy, $localId, $remoteId, $orderedQty, &$product, &$oldQty, &$newQty) {
            $query = PharmaProduct::where('pharmacy_id', $pharmacy->id);

            if ($localId) {
                $query->where(function ($q) use ($localId) {
                    $q->where('remote_id', $localId)
                      ->orWhere('id', $localId);
                });
            } elseif ($remoteId) {
                $query->where('remote_id', $remoteId);
            } else {
                return;
            }

            $product = $query->lockForUpdate()->first();

            if (! $product) {
                return;
            }

            $oldQty = (int) ($product->quantity ?? 0);
            $newQty = max(0, $oldQty - $orderedQty);

            $product->quantity  = $newQty;
            $product->stock     = $newQty > 0 ? 'Disponible' : 'Rupture';
            $product->synced_at = now();
            $product->save();
        });

        if (! $product) {
            Log::warning("❌ Produit introuvable pour la ligne", [
                'order_id'    => $order->id,
                'pharmacy_id' => $pharmacy->id,
                'localId'     => $localId,
                'remoteId'    => $remoteId
            ]);
            continue;
        }

        Log::info("✅ Produit décrémenté localement", [
            'order_id'      => $order->id,
            'pharmacy_id'   => $pharmacy->id,
            'libelle'       => $product->libelle,
            'ancienne_qte'  => $oldQty,
            'nouvelle_qte'  => $newQty,
            'ordered_qty'   => $orderedQty,
        ]);

        // 🔄 Synchronisation côté AbdouPharma (multi-tenant, token de la pharmacie)
        try {
            $remoteSync->decrementProduct(
                $pharmacy,
                $product->remote_id ?? $localId,
                $orderedQty
            );
        } catch (\Throwable $e) {
            Log::error("⚠️ Échec de la décrémentation distante", [
                'order_id'    => $order->id,
                'pharmacy_id' => $pharmacy->id,
                'error'       => $e->getMessage(),
                'product'     => $product->libelle,
            ]);
        }
    }
}
    
    /**
     * Essaie de récupérer les lignes de commande, peu importe le champ utilisé.
     * On gère aussi les champs JSON stockés en string.
     */
    protected function extractOrderLines(CustomerOrder $order): array
    {
        $lines = [];

        // champs possibles dans ton projet
        $candidates = [
            'items',
            'items_json',
            'cart',
            'cart_json',
            'payload',
            'products',
            'produits',
        ];

        foreach ($candidates as $field) {
            if (!isset($order->$field) || $order->$field === null) {
                continue;
            }

            $value = $order->$field;

            // si c’est du JSON en string
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    $lines = array_merge($lines, $decoded);
                }
                continue;
            }

            // si c’est déjà un tableau
            if (is_array($value)) {
                $lines = array_merge($lines, $value);
                continue;
            }
        }

        // si jamais plus tard tu ajoutes une vraie relation
        if (method_exists($order, 'items')) {
            try {
                $rel = $order->items()->get()->toArray();
                $lines = array_merge($lines, $rel);
            } catch (\Throwable $e) {
                // on ignore, c’est juste pour être tolérant
            }
        }

        return $lines;
    }
}
