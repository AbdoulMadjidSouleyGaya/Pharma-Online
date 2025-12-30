<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\PharmaProduct;
use App\Models\StockAlert;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierMessage;
use App\Mail\SupplierContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockController extends Controller
{
    /**
     * Page principale de gestion de stock pour le pharmacien.
     */
    public function index(Request $request)
    {
        $pharmacy = $request->user()->pharmacy;

        $query = PharmaProduct::with('supplier')
            ->where('pharmacy_id', $pharmacy->id);

        // 🔎 Recherche
        if ($search = $request->get('q')) {
            $query->where('libelle', 'like', '%' . $search . '%');
        }

        // 🎚 Filtre état (stock bas / rupture)
        if ($state = $request->get('state')) {
            if ($state === 'low') {
                $query->whereColumn('quantity', '<=', 'min_quantity')
                      ->where('quantity', '>', 0);
            } elseif ($state === 'out') {
                $query->where('quantity', '<=', 0);
            }
        }

        $products = $query->orderBy('libelle')
            ->paginate(15)
            ->withQueryString();

        // 📊 Statistiques globales
        $totalProducts = PharmaProduct::where('pharmacy_id', $pharmacy->id)->count();

        $lowStockCount = PharmaProduct::where('pharmacy_id', $pharmacy->id)
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->where('quantity', '>', 0)
            ->count();

        $outOfStockCount = PharmaProduct::where('pharmacy_id', $pharmacy->id)
            ->where('quantity', '<=', 0)
            ->count();

        // 📇 Fournisseurs de la pharmacie
        $suppliers = Supplier::where('pharmacy_id', $pharmacy->id)
            ->orderBy('name')
            ->get();

        return view('pharmacist.stock.index', compact(
            'pharmacy',
            'products',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'suppliers'
        ));
    }

    /**
     * Enregistrer une entrée de stock manuelle (réception fournisseur).
     */
    public function storeEntry(Request $request)
    {
        $pharmacy = $request->user()->pharmacy;

        $data = $request->validate([
            'pharma_product_id' => ['required', 'exists:pharma_products,id'],
            'supplier_id'       => ['nullable', 'exists:suppliers,id'],
            'quantity'          => ['required', 'integer', 'min:1'],
            'comment'           => ['nullable', 'string', 'max:1000'],
        ]);

        // On s'assure que le produit appartient bien à la pharmacie du pharmacien connecté
        $product = PharmaProduct::where('pharmacy_id', $pharmacy->id)
            ->findOrFail($data['pharma_product_id']);

        $previous = (int) $product->quantity;
        $new      = $previous + $data['quantity'];

        // 🔄 Mise à jour de la quantité + éventuel fournisseur
        $product->quantity = $new;

        if (!empty($data['supplier_id'])) {
            $product->supplier_id = $data['supplier_id'];
        }

        // Met à jour le champ stock (Disponible / Stock bas / Rupture)
        $product->refreshStockFromQuantity();
        $product->save();

        // 🧾 Log du mouvement de stock
        StockMovement::create([
            'pharmacy_id'       => $pharmacy->id,
            'pharma_product_id' => $product->id,
            'user_id'           => $request->user()->id,
            'type'              => 'IN', // cohérent avec decrementAndLog (OUT)
            'quantity'          => $data['quantity'],
            'previous_quantity' => $previous,
            'new_quantity'      => $new,
            'source'            => 'manual',
            'reference'         => null,
            'comment'           => $data['comment'] ?? 'Réception fournisseur',
        ]);

        return back()->with('success', 'Entrée de stock enregistrée avec succès.');
    }

    /**
     * Historique des mouvements de stock pour un produit.
     */
    public function movements(Request $request, PharmaProduct $product)
    {
        $pharmacy = $request->user()->pharmacy;

        abort_unless($product->pharmacy_id === $pharmacy->id, 403);

        $movements = StockMovement::where('pharmacy_id', $pharmacy->id)
            ->where('pharma_product_id', $product->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('pharmacist.stock.movements', compact('product', 'movements'));
    }

    /**
     * Liste des alertes de stock pour le pharmacien connecté.
     * Les alertes non lues sont marquées comme lues à l'ouverture.
     */
    public function alerts(Request $request)
    {
        $user = $request->user();

        $alerts = StockAlert::with('product')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        // Marquer comme lues
        StockAlert::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('pharmacist.stock.alerts', compact('alerts'));
    }

    /**
     * Méthode utilitaire à appeler lorsqu'une commande client consomme du stock.
     *
     * Exemple d'utilisation :
     * StockController::decrementAndLog($product, $qty, $order->number, auth()->id());
     */
    public static function decrementAndLog(
        PharmaProduct $product,
        int $orderedQty,
        ?string $orderNumber = null,
        ?int $userId = null
    ): void {
        $previous = (int) $product->quantity;
        $new      = max(0, $previous - $orderedQty);

        $product->quantity = $new;
        $product->refreshStockFromQuantity();
        $product->save();

        StockMovement::create([
            'pharmacy_id'       => $product->pharmacy_id,
            'pharma_product_id' => $product->id,
            'user_id'           => $userId,
            'type'              => 'OUT',
            'quantity'          => -$orderedQty,
            'previous_quantity' => $previous,
            'new_quantity'      => $new,
            'source'            => 'order',
            'reference'         => $orderNumber,
            'comment'           => 'Sortie suite à une commande client',
        ]);

        // 🔔 Notifications automatiques si passage en Stock bas / Rupture
        StockAlert::notifyPharmacistsIfNeeded($product, $previous, $new);
    }

    /**
     * Contact d'un fournisseur pour un produit donné.
     */
    public function contactSupplier(Request $request)
    {
        $data = $request->validate([
            'product_id'  => ['required', 'exists:pharma_products,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'subject'     => ['required', 'string', 'max:255'],
            'message'     => ['required', 'string', 'min:10'],
        ]);

        $user     = $request->user();
        $product  = PharmaProduct::findOrFail($data['product_id']);
        $supplier = Supplier::findOrFail($data['supplier_id']);

        $msg = SupplierMessage::create([
            'pharmacy_id' => $user->pharmacy_id,
            'product_id'  => $product->id,
            'supplier_id' => $supplier->id,
            'user_id'     => $user->id,
            'subject'     => $data['subject'],
            'message'     => $data['message'],
        ]);

        try {
            Mail::to($supplier->email)->send(new SupplierContact($msg));
            $msg->status = 'sent';
            $msg->save();
        } catch (\Exception $e) {
            $msg->status = 'failed';
            $msg->save();
        }

        return back()->with('success', 'Message envoyé au fournisseur.');
    }

    /**
     * Exporter le stock au format CSV (ouvrable dans Excel).
     */
    public function export(Request $request): StreamedResponse
    {
        $pharmacy = $request->user()->pharmacy;

        $fileName = 'stock_pharmacie_'.$pharmacy->id.'_'.now()->format('Ymd_His').'.csv';

        $products = PharmaProduct::with('supplier')
            ->where('pharmacy_id', $pharmacy->id)
            ->orderBy('libelle')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');

            // Si tu veux, tu peux ajouter un BOM pour Excel :
            // fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes du fichier
            fputcsv($handle, [
                'ID',
                'Libellé',
                'Prix (FCFA)',
                'Quantité',
                'État',
                'Fournisseur',
            ], ';');

            foreach ($products as $product) {
                $qty  = (int) $product->quantity;
                $min  = 20;
                $etat = 'Disponible';

                if ($qty <= 0) {
                    $etat = 'Rupture';
                } elseif ($qty <= $min) {
                    $etat = 'Stock bas';
                }

                // Fournisseur : relation locale en priorité, sinon supplier_name importé d'AbdouPharma
                $fournisseur = optional($product->supplier)->name ?: ($product->supplier_name ?? '');

                fputcsv($handle, [
                    $product->id,
                    $product->libelle,
                    $product->prix,
                    $qty,
                    $etat,
                    $fournisseur,
                ], ';');
            }

            fclose($handle);
        }, $fileName, $headers);
    }
}
