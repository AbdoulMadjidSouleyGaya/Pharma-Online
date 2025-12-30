<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $fillable = ['user_id','title','body','read_at'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}