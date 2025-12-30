<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'pharma_product_id',
        'user_id',
        'level',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PharmaProduct::class, 'pharma_product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Crée des alertes pour tous les pharmaciens de la pharmacie concernée,
     * si le stock vient de passer en "stock bas" ou "rupture".
     */
    public static function notifyPharmacistsIfNeeded(PharmaProduct $product, int $previousQty, int $newQty): void
    {
        $pharmacy = $product->pharmacy;
        if (! $pharmacy) {
            return;
        }

        // Seuil minimal fixé à 20
        $threshold = 20;

        $level = null;
        $message = null;

        // Passage en rupture : de > 0 à <= 0
        if ($newQty <= 0 && $previousQty > 0) {
            $level = 'out';
            $message = "Le produit « {$product->libelle} » est en rupture de stock.";
        }
        // Passage en stock bas : de > seuil à <= seuil (mais encore > 0)
        elseif ($newQty > 0 && $newQty <= $threshold && $previousQty > $threshold) {
            $level = 'low';
            $message = "Le produit « {$product->libelle} » est en stock bas (stock = {$newQty}).";
        }

        if (! $level) {
            return;
        }

        // Récupérer tous les pharmaciens de cette pharmacie
        // (on suppose que users ont pharmacy_id + rôle pharmacien/pharmacist)
        $pharmacists = \App\Models\User::where('pharmacy_id', $pharmacy->id)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['pharmacist', 'pharmacien']);
            })
            ->get();

        foreach ($pharmacists as $user) {
            self::create([
                'pharmacy_id'       => $pharmacy->id,
                'pharma_product_id' => $product->id,
                'user_id'           => $user->id,
                'level'             => $level,
                'message'           => $message,
                'is_read'           => false,
            ]);
        }
    }
}
