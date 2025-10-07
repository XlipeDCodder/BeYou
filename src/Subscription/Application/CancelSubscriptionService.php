<?php

namespace Beyou\Subscription\Application;

use App\Models\User;
use Beyou\Catalog\Domain\Model\Channel;
use Beyou\Subscription\Domain\Enums\SubscriptionStatus;

class CancelSubscriptionService
{
    public function execute(User $user, Channel $channel): void
    {
        $user->subscriptions()
            ->where('channel_id', $channel->id)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->firstOrFail()
            ->update(['status' => SubscriptionStatus::CANCELLED]);
    }
}