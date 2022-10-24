<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GamesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/games/before_release', [GamesController::class, 'getBeforeReleaseGames']);

Route::post('/register/device', [AuthController::class, 'registerDeviceInfo']);

Route::post('/games/add/favorite', [GamesController::class, 'addFavoriteGame']);

Route::post('/games/remove/favorite', [GamesController::class, 'removeFavoriteGame']);
