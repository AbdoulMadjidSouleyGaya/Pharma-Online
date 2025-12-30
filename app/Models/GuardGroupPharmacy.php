<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardGroupPharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'guard_group_id','name','district','address','phone'
    ];

    public function group()
    {
        return $this->belongsTo(GuardGroup::class, 'guard_group_id');
    }
}
