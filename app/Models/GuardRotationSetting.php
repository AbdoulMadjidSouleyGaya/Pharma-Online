<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardRotationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'rotation_start_at','start_group','cycle_length','period_days','region'
    ];

    protected $casts = [
        'rotation_start_at' => 'datetime',
    ];
}
