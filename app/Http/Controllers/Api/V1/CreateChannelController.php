<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Beyou\Catalog\Application\CreateChannelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreateChannelController extends Controller
{
    public function __construct(
        private readonly CreateChannelService $createChannelService
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:channels,name'],
        ]);

        
        $channel = $this->createChannelService->execute(
            $request->user(),
            $validated['name']
        );

        
        return response()->json([
            'message' => 'Canal criado com sucesso!',
            'data' => $channel,
        ], Response::HTTP_CREATED); 
    }
}