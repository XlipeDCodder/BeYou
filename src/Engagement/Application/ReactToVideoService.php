<?php

namespace Beyou\Engagement\Application;

use App\Models\User;
use Beyou\Catalog\Domain\Model\Video;
use Beyou\Engagement\Domain\Model\VideoReaction;

class ReactToVideoService
{
    public function execute(User $user, Video $video, string $reactionType): VideoReaction
    {
        return VideoReaction::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $video->id],
            ['type' => $reactionType]
        );
    }
}