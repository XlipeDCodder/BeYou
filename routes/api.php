<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CreateChannelController;
use App\Http\Controllers\Api\V1\StoreVideoController;
use App\Http\Controllers\Api\V1\ListVideoController;
use App\Http\Controllers\Api\V1\ShowVideoController;
use App\Http\Controllers\Api\V1\ShowChannelController;
use App\Http\Controllers\Api\V1\SearchController;


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
});