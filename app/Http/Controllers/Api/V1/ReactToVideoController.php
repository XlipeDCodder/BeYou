<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Beyou\Catalog\Domain\Model\Video;
use Beyou\Engagement\Application\ReactToVideoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReactToVideoController extends Controller
{
    public function __construct(private readonly ReactToVideoService $reactToVideoService)
    {
    }

    public function __invoke(Request $request, Video $video, string $reactionType)
    {
        $this->reactToVideoService->execute($request->user(), $video, $reactionType);

        return response()->noContent();
    }
}