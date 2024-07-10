<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ChapitreController;
use App\Http\Controllers\MatiereDeLaClasseController;
use App\Http\Controllers\LeconController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\OtpController;

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
    Route::post('/verify-otp', [AuthController::class,'verifyOtp']);
    Route::post('/get-otp', [AuthController::class,'getOtp']);
    Route::post('/login', [AuthController::class,'login']);
    Route::post('/edit-password', [AuthController::class,'editPassword']);

    Route::middleware(['auth:api'])->group(function () {
        Route::get('/users', [AuthController::class,'index']);
        Route::get('/users/auth', [AuthController::class,'userAuth']);
        Route::post('/users/update', [AuthController::class,'update']);
        Route::post('/users/changePassword', [AuthController::class,'changePassword']);
        Route::post('/users/get', [AuthController::class,'userBy']);
        Route::post('/users/disable', [AuthController::class,'disable']);

        Route::post('/users/change-active-statut', [AuthController::class,'changeActiveStatus']);
        Route::post('/users/change-block-statut', [AuthController::class,'changeActiveStatus']);

        Route::get('/classes/{slug}/matieres', [ClasseController::class,'getClasseMatiere']);
        Route::get('/classes/{classe}/matieres/{matiere}/chapitres', [ClasseController::class,'getClasseMatiereChapitres']);

        Route::resources([
            'classes' => ClasseController::class,
            'matieres' => MatiereController::class,
            'periodes' => PeriodeController::class,
            'matiere-de-la-classe' => MatiereDeLaClasseController::class,
            'chapitres' => ChapitreController::class,
            'lecons' => LeconController::class,
            'cours' => CoursController::class,

        ]);
    });
});
