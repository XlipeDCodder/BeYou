<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CommentResource;
use Beyou\Catalog\Domain\Model\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListCommentsController extends Controller
{
public function __invoke(Request $request, Video $video): AnonymousResourceCollection
{
    $comments = $video->comments()
        ->with('user')
        ->whereNull('parent_id')
        ->latest()
        ->paginate(10);

    
    $resourceCollection = CommentResource::collection($comments);

    
    $resourceCollection->response()->header('Cache-Control', 'no-cache, private');

    
    return $resourceCollection;
}
}