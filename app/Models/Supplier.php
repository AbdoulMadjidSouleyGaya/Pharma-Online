<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'name',
        'contact',   // ✅ important
        'phone',
        'email',
        'address',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(\App\Models\Pharmacy::class);
    }

    public function products()
    {
        return $this->hasMany(\App\Models\PharmaProduct::class);
    }
}
