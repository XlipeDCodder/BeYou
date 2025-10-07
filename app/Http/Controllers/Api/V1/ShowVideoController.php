<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\VideoResource;
use Beyou\Catalog\Domain\Enums\VideoStatus;
use Beyou\Catalog\Domain\Model\Video;
use Illuminate\Http\Request;

class ShowVideoController extends Controller
{
    public function __invoke(Request $request, Video $video): VideoResource
    {

        if ($video->status !== VideoStatus::PUBLISHED) {
            abort(404);
        }

       
        $video->load('channel');

        return new VideoResource($video);
    }
}