<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CreateChannelController;
use App\Http\Controllers\Api\V1\StoreVideoController;
use App\Http\Controllers\Api\V1\ListVideoController;
use App\Http\Controllers\Api\V1\ShowVideoController;
use App\Http\Controllers\Api\V1\ShowChannelController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\ReactToVideoController;
use App\Http\Controllers\Api\V1\RemoveVideoReactionController;
use Beyou\Catalog\Domain\Model\Video;
use App\Http\Controllers\Api\V1\SubscribeToChannelController;
use App\Http\Controllers\Api\V1\CancelSubscriptionController;


Route::get('/search', SearchController::class);
Route::get('/channels/{channel}', ShowChannelController::class);
Route::get('/videos', ListVideoController::class);
Route::get('/videos/{video:uuid}', ShowVideoController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/channels', CreateChannelController::class);
    Route::post('/videos', StoreVideoController::class);
    Route::post('/videos/{video:uuid}/like', fn(Request $request, Video $video) => (new ReactToVideoController(new \Beyou\Engagement\Application\ReactToVideoService()))($request, $video, 'like'));
    Route::post('/videos/{video:uuid}/dislike', fn(Request $request, Video $video) => (new ReactToVideoController(new \Beyou\Engagement\Application\ReactToVideoService()))($request, $video, 'dislike'));
    Route::delete('/videos/{video:uuid}/reaction', RemoveVideoReactionController::class);
    Route::post('/channels/{channel}/subscribe', SubscribeToChannelController::class);
    Route::delete('/channels/{channel}/subscribe', CancelSubscriptionController::class);
});