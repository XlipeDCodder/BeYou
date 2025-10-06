<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreVideoRequest;
use Beyou\Catalog\Application\UploadVideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StoreVideoController extends Controller
{
    public function __construct(
        private readonly UploadVideoService $uploadVideoService
    ) {
    }

    public function __invoke(StoreVideoRequest $request): JsonResponse
    {
        
        $validatedData = $request->validated();
        $videoFile = $request->file('video');
        $user = $request->user();

        
        $video = $this->uploadVideoService->execute(
            $user->channel, 
            $validatedData,
            $videoFile
        );


        return response()->json([
            'message' => 'Upload recebido! O vídeo está sendo processado.',
            'data' => $video,
        ], Response::HTTP_ACCEPTED);
    }
}