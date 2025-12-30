<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'region','week_label','starts_at','ends_at','image_path','ocr_text','created_by'
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
    ];

    public function pharmacies() {
        return $this->hasMany(GuardPharmacy::class);
    }
}
