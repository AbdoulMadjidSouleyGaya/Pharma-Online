<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierMessage extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'product_id',
        'supplier_id',
        'user_id',
        'subject',
        'message',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(PharmaProduct::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
