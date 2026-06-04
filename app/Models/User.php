<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

//relationship with other models
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[UseResource(UserResource::class)]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function task(): HasMany
    {
        return $this->hasMany(task::class);
    }

    public function latestTask(): HasOne
    {
        return $this->hasOne(task::class)->latestOfMany();
    }

    public function category(): HasMany
    {
        return $this->hasMany(category::class);
    }

    public function phone(): HasOne
    {
        return $this->hasOne(phone::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(roles::class, 'roles_users')->withPivot('created_at');
    }

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
