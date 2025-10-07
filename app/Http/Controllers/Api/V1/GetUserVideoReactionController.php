<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Beyou\Catalog\Domain\Model\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GetUserVideoReactionController extends Controller
{
    // Garanta que o nome deste método é exatamente "__invoke"
    public function __invoke(Request $request, Video $video): JsonResponse
    {
        $reaction = $video->reactions()
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json([
            'reaction' => $reaction?->type, // Retorna 'like', 'dislike' ou null
        ]);
    }
}