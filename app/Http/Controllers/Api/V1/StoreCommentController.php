<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CommentResource;
use Beyou\Catalog\Domain\Model\Video;
use Illuminate\Http\Request;

class StoreCommentController extends Controller
{
    public function __invoke(Request $request, Video $video)
    {
        
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2500'],
        ]);

        
        $comment = $video->comments()->create([
            'body' => $validated['body'],
            'user_id' => $request->user()->id,
        ]);

        
        $comment->load('user');

        
        return new CommentResource($comment);
    }
}