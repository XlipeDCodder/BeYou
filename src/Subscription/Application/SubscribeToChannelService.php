<?php

namespace Beyou\Subscription\Application;

use App\Models\User;
use Beyou\Catalog\Domain\Model\Channel;
use Beyou\Subscription\Domain\Enums\SubscriptionStatus;
use Beyou\Subscription\Domain\Model\Subscription;

class SubscribeToChannelService
{
    public function execute(User $user, Channel $channel): Subscription
    {
        
        return Subscription::updateOrCreate(
            ['user_id' => $user->id, 'channel_id' => $channel->id],
            [
                'status' => SubscriptionStatus::ACTIVE,
                'expires_at' => now()->addMonth(),
            ]
        );
    }
}