<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Beyou\Catalog\Domain\Model\Video;
use Beyou\Engagement\Application\RemoveVideoReactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RemoveVideoReactionController extends Controller
{
    public function __construct(private readonly RemoveVideoReactionService $removeVideoReactionService)
    {
    }

    public function __invoke(Request $request, Video $video)
    {
        $this->removeVideoReactionService->execute($request->user(), $video);

        return response()->noContent();
    }
}