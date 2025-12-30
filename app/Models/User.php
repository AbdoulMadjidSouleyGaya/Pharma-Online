<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'pharmacy_id',
    'password_is_temp',          // ✅ nécessaire
    'temp_password_expires_at',  // ✅ nécessaire
];
protected $casts = [
    'email_verified_at'        => 'datetime',
    'password_is_temp'         => 'bool',
    'temp_password_expires_at' => 'datetime',
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function roles()
{
    return $this->belongsToMany(\App\Models\Role::class);
}

public function hasRole(string $name): bool{
    return $this->roles()->where('name',$name)->exists();
}
// Option A : clé étrangère directe
// users.pharmacy_id -> pharmacies.id
public function pharmacy()
{
    return $this->belongsTo(\App\Models\Pharmacy::class);
}


// Option B : pivot (si un pharmacien peut appartenir à plusieurs pharmacies)
public function pharmacies() {
    return $this->belongsToMany(\App\Models\Pharmacy::class);
}


}
