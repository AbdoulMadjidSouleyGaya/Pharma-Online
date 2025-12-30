<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TwoFactorCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','purpose','code','expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }
}
