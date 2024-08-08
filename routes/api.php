<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/',[AuthController::class,'test']);
Route::post('signup',[AuthController::class,'signup']);
Route::post('login',[AuthController::class,'login']);

// Route::post('logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

// Route::apiResource('post',PostController::class)->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('post',PostController::class);
    Route::post('logout',[AuthController::class,'logout']);
});