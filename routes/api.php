<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CancelSubscriptionController;
use App\Http\Controllers\Api\V1\CreateChannelController;
use App\Http\Controllers\Api\V1\GetUserVideoReactionController; // <-- A LINHA QUE FALTAVA
use App\Http\Controllers\Api\V1\ListVideoController;
use App\Http\Controllers\Api\V1\ReactToVideoController;
use App\Http\Controllers\Api\V1\RemoveVideoReactionController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\ShowChannelController;
use App\Http\Controllers\Api\V1\ShowVideoController;
use App\Http\Controllers\Api\V1\StoreVideoController;
use App\Http\Controllers\Api\V1\SubscribeToChannelController;
use Beyou\Catalog\Domain\Model\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- ROTAS DE AUTENTICAÇÃO (PÚBLICAS) ---
Route::post('/auth/login', [AuthController::class, 'login']);

// --- ROTAS PÚBLICAS (NÃO PRECISAM DE LOGIN) ---
Route::get('/search', SearchController::class);
Route::get('/channels/{channel}', ShowChannelController::class);
Route::get('/videos', ListVideoController::class);
Route::get('/videos/{video:uuid}', ShowVideoController::class);

// --- ROTAS PROTEGIDAS (PRECISAM DE TOKEN JWT) ---
Route::middleware('auth:api')->group(function () {
    // Rotas de Autenticação Protegidas
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Rotas de Canais e Vídeos
    Route::post('/channels', CreateChannelController::class);
    Route::post('/videos', StoreVideoController::class);

    // Rotas de Reação
    Route::get('/videos/{video:uuid}/reaction', GetUserVideoReactionController::class);
    Route::post('/videos/{video:uuid}/like', function (Request $request, Video $video) {
        return app(ReactToVideoController::class)($request, $video, 'like');
    });
    Route::post('/videos/{video:uuid}/dislike', function (Request $request, Video $video) {
        return app(ReactToVideoController::class)($request, $video, 'dislike');
    });
    Route::delete('/videos/{video:uuid}/reaction', RemoveVideoReactionController::class);

    // Rotas de Inscrição
    Route::post('/channels/{channel}/subscribe', SubscribeToChannelController::class);
    Route::delete('/channels/{channel}/subscribe', CancelSubscriptionController::class);
});