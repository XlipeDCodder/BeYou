<?php

namespace Beyou\Subscription\Domain\Model;

use App\Models\User;
use Beyou\Catalog\Domain\Model\Channel;
use Beyou\Subscription\Domain\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'channel_id',
        'status',
        'expires_at',
        'gateway_subscription_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'status' => SubscriptionStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}