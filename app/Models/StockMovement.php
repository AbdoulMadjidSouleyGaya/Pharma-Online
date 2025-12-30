<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'pharma_product_id',
        'user_id',
        'type',
        'quantity',
        'previous_quantity',
        'new_quantity',
        'source',
        'reference',
        'comment',
    ];

    public function product()
    {
        return $this->belongsTo(PharmaProduct::class, 'pharma_product_id');
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
