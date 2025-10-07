<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function channel(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\Beyou\Catalog\Domain\Model\Channel::class);
    }
    
    public function videoReactions(): HasMany
    {
        return $this->hasMany(\Beyou\Engagement\Domain\Model\VideoReaction::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(\Beyou\Subscription\Domain\Model\Subscription::class);
    }
}
