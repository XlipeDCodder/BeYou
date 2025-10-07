<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\VideoResource;
use Beyou\Catalog\Domain\Enums\VideoStatus;
use Beyou\Catalog\Domain\Model\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListVideoController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        
        $videos = Video::query()
            
            ->where('status', VideoStatus::PUBLISHED)
            
            ->latest('published_at')
            
            ->paginate(12);

        
        return VideoResource::collection($videos);
    }
}