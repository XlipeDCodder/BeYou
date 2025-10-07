<?php

namespace Beyou\Engagement\Application;

use App\Models\User;
use Beyou\Catalog\Domain\Model\Video;

class RemoveVideoReactionService
{
    public function execute(User $user, Video $video): void
    {
        $user->videoReactions()->where('video_id', $video->id)->delete();
    }
}