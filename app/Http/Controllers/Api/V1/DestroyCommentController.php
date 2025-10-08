<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Beyou\Engagement\Domain\Model\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DestroyCommentController extends Controller
{
    public function __invoke(Request $request, Comment $comment): Response
    {
        
        $this->authorize('delete', $comment);

        
        $comment->delete();

        
        return response()->noContent();
    }
}