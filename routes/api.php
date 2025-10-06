<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CreateChannelController;
use App\Http\Controllers\Api\V1\StoreVideoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('/channels', CreateChannelController::class);
Route::middleware('auth:sanctum')->post('/videos', StoreVideoController::class);
