<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardListController;
use App\Http\Controllers\CardController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1'], function() {
    Route::group(['prefix' => 'auth'], function() {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::get('logout', [AuthController::class, 'logout']);
    });

    Route::resource('board', BoardController::class)->only(['index', 'store', 'destroy']);

    Route::group(['middleware' => 'member'], function() {
        Route::resource('board', BoardController::class)->only(['update', 'show']);
        Route::post('board/{board}/member', [BoardController::class, 'add']);
        Route::delete('board/{board}/member/{user}', [BoardController::class, 'remove']);
        Route::resource('board/{board}/list', BoardListController::class);
        Route::post('board/{board}/list/{list}/right', [BoardListController::class, 'right']);
        Route::post('board/{board}/list/{list}/left', [BoardListController::class, 'left']);
        Route::resource('board/{board}/list/{list}/card', CardController::class);
        Route::post('card/{card}/up', [CardController::class, 'up']);
        Route::post('card/{card}/down', [CardController::class, 'down']);
        Route::post('card/{card}/move/{list}', [CardController::class, 'move']);
    });
});