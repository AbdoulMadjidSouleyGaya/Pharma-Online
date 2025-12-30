<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'district',
        'address',
        'phone',
        'email',
        'created_by',
        'api_token',   // ← ajouté
    ];

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function pharmacists()
    {
        return $this->belongsToMany(\App\Models\User::class);
    }
    public function pharmaProducts()
    {
        return $this->hasMany(\App\Models\PharmaProduct::class);
    }
    public function availableProducts()
{
    return $this->hasMany(\App\Models\PharmaProduct::class)
        ->where('stock', 'Disponible');
}

}
