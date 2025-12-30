<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pharmacy;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmaProduct extends Model
{
    protected $fillable = [
        'remote_id',
        'libelle',
        'prix',
        'stock',       // Disponible | Stock bas | Rupture
        'quantity',
        'synced_at',
        'pharmacy_id',
        'image',
        'description',
        'min_quantity',
        'supplier_id',
        'expires_at',
        'location',
        'supplier_name', // 🆕 Nom du fournisseur venant d'AbdouPharma
    ];

    protected $casts = [
        'synced_at'    => 'datetime',
        'expires_at'   => 'date',
        'quantity'     => 'integer',
        'min_quantity' => 'integer',
        'pharmacy_id'  => 'integer',
        'supplier_id'  => 'integer',
    ];

    /**
     * Met à jour le stock logique en fonction de la quantité
     * et du seuil min (min_quantity).
     *
     * - quantity <= 0                  → Rupture
     * - 0 < quantity <= min_quantity  → Stock bas
     * - quantity  > min_quantity      → Disponible
     */
    public function refreshStockFromQuantity(): void
    {
        $qty = (int) $this->quantity;
        $min = (int) ($this->min_quantity ?? 20);

        if ($qty <= 0) {
            $this->stock = 'Rupture';
        } elseif ($qty <= $min) {
            $this->stock = 'Stock bas';
        } else {
            $this->stock = 'Disponible';
        }
    }

    /**
     * Pharmacie à laquelle appartient ce produit.
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    /**
     * Fournisseur principal (optionnel) du produit.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Historique des mouvements de stock pour ce produit.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'pharma_product_id');
    }
}
