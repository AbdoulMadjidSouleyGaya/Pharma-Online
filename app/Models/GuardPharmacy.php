<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardPharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'guard_schedule_id','name','district','address','phone'
    ];

    public function schedule() {
        return $this->belongsTo(GuardSchedule::class, 'guard_schedule_id');
    }
}
