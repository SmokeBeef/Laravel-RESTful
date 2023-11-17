<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     echo 'midleware';
//     return $request->user();
// });

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);
Route::get('/users', [UserController::class, 'getAllUsers']);
Route::get('/redis', function(){
    $redis = Redis::incr('p');
    return $redis;
});

Route::middleware(ApiAuthMiddleware::class)->group(function(){
    Route::get("/users/current", [UserController::class, 'get']);
    Route::put("/users", [UserController::class, 'updateUser']);
    Route::delete("/users/logout", [UserController::class, 'logout']);
});