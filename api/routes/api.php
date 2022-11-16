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

// 発売前のゲーム一覧取得
Route::get('/games/info', [GamesController::class, 'getGamesInfo']);

// 発売済みのゲーム一覧取得
// Route::get('/games/released', [GamesController::class, 'getReleasedGames']);

// ゲームお気に入り一覧取得
Route::get('/games/favorite', [GamesController::class, 'getFavoriteGameList']);

// ゲーム詳細取得
Route::get('/games/detail', [GamesController::class, 'getGamesDetail']);

// デバイス登録
Route::post('/register/device', [AuthController::class, 'registerDeviceInfo']);

// ゲームお気に入り登録
Route::post('/games/add/favorite', [GamesController::class, 'addFavoriteGame']);

// ゲームお気に入り解除
Route::post('/games/remove/favorite', [GamesController::class, 'removeFavoriteGame']);
