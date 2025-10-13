<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CommentResource;
use Beyou\Engagement\Domain\Model\Comment;
use Illuminate\Http\Request;

class ListCommentRepliesController extends Controller
{
    public function __invoke(Request $request, Comment $comment)
    {
        $replies = $comment->replies()
            ->with('user')
            ->latest()
            ->paginate(10); 

        return CommentResource::collection($replies);
    }
}