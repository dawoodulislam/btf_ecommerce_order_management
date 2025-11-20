<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'provider'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    //----------------------------------------------------------------------
    // Relationships
    //----------------------------------------------------------------------

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    //----------------------------------------------------------------------
    // Role Helpers
    //----------------------------------------------------------------------

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function assignRole(string $roleName)
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            throw new \Exception("Role '{$roleName}' not found.");
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
        return $this;
    }

    //----------------------------------------------------------------------
    // JWT Authentication
    //----------------------------------------------------------------------

    /**
     * Get the identifier that will be stored in the JWT subject claim.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Add custom claims to JWT payload.
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'roles' => $this->roles->pluck('name')
        ];
    }

    //----------------------------------------------------------------------
    // Password Mutator
    //----------------------------------------------------------------------

    public function setPasswordAttribute($value)
    {
        if ($value && strlen($value) < 60) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
