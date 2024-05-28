<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClasseController;

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


Route::group(['middleware' => ['cors','json.response']], function () {
    Route::post('/register', [AuthController::class,'register']);
    Route::post('/login', [AuthController::class,'login']);

    Route::middleware(['auth:api'])->group(function () {
        Route::get('/users', [AuthController::class,'index']);
        Route::get('/users/auth', [AuthController::class,'userAuth']);
        Route::post('/users/update', [AuthController::class,'update']);
        Route::post('/users/changePassword', [AuthController::class,'changePassword']);
        Route::post('/users/get', [AuthController::class,'userBy']);
        Route::post('/users/disable', [AuthController::class,'disable']);

        Route::resources([
            'classes' => ClasseController::class,

        ]);
    });
});
