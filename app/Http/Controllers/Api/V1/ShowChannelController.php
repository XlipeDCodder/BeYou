<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ChannelResource;
use Beyou\Catalog\Domain\Enums\VideoStatus;
use Beyou\Catalog\Domain\Model\Channel;
use Illuminate\Http\Request;

class ShowChannelController extends Controller
{
    public function __invoke(Request $request, Channel $channel): ChannelResource
    {

        $videos = $channel->videos()
            ->where('status', VideoStatus::PUBLISHED)
            ->latest('published_at')
            ->paginate(12);

        
        $channel->setRelation('videos', $videos);

        return new ChannelResource($channel);
    }
}