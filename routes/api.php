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
use App\Http\Controllers\FileController;
use App\Http\Controllers\EvaluationLeconController;
use App\Http\Controllers\QuestionLeconController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;

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
        Route::post('/users/change-block-statut', [AuthController::class,'changeBlockStatus']);
        Route::get('/users/profile/{profile}', [AuthController::class,'getUserByProfile']);

        Route::get('/classes/{slug}/matieres', [ClasseController::class,'getClasseMatiere']);
        Route::get('/classes/{classe}/matieres/{matiere}/chapitres', [ClasseController::class,'getClasseMatiereChapitres']);



        Route::get('/matiere-de-la-classe/classe/{slug}', [MatiereDeLaClasseController::class,'getMatiereDeLaClasseByClasseSlug']);
        Route::get('/utilisateurs/profile/{slug}', [UtilisateurController::class,'getUtilisateurByProfile']);
        Route::get('/evaluations-lecons/lecon/{slug}', [EvaluationLeconController::class,'getEvaluationByLeconSlug']);


        /**Matières */
        Route::get('/matieres/classe/{slugClasse}', [MatiereController::class,'getMatiereByClasse']);


        /**Chapitres */
        Route::get('/chapitres/classe/{slugClasse}', [ChapitreController::class,'getChapitreByClasse']);
        Route::get('/chapitres/classe/{slugClasse}/matiere/{slugMatiere}', [ChapitreController::class,'getChapitreByClasseMatiere']);
        Route::post('/chapitres/import', [ChapitreController::class,'importChapitre']);

        /**Leçons */
        Route::get('/lecons/chapitre/{slug}', [LeconController::class,'getLeconByChapitreSlug']);
        Route::get('/lecons/periode/{slugPeriode}', [LeconController::class,'getLeconByPeriode']);
        
        Route::get('/lecons/classe/{slugClasse}', [LeconController::class,'getLeconByClasse']);
        Route::get('/lecons/classe/{slugClasse}/periode/{slugPeriode}', [LeconController::class,'getLeconByClassePeriode']);
        Route::get('/lecons/classe/{slugClasse}/periode/{slugPeriode}/matiere/{slugMatiere}', [LeconController::class,'getLeconByClassePeriodeMatiere']);
        Route::get('/lecons/classe/{slugClasse}/periode/{slugPeriode}/matiere/{slugMatiere}/chapitre/{slugChapitre}', [LeconController::class,'getLeconByClassePeriodeMatiereChapitre']);

        Route::get('/chapitres/classe/{slugClasse}/periode/{slugPeriode}', [ChapitreController::class,'getChapitreByPeriodeClasse']);


        Route::resources([
            'utilisateurs' => UtilisateurController::class,
            'classes' => ClasseController::class,
            'matieres' => MatiereController::class,
            'periodes' => PeriodeController::class,
            'matiere-de-la-classe' => MatiereDeLaClasseController::class,
            'chapitres' => ChapitreController::class,
            'lecons' => LeconController::class,
            'cours' => CoursController::class,
            'evaluations-lecons' => EvaluationLeconController::class,
            'questions-lecons' => QuestionLeconController::class,
            'chatgpt' => ChatController::class,

        ]);


    Route::get('/files', [LeconController::class, 'getFile'])->name('files.index');
    Route::get('/files/lecon/{slug}', [LeconController::class, 'getLeconFile'])->name('files.filesLecon');
    Route::post('/files', [LeconController::class, 'storeFile'])->name('files.store');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::post('/questions-lecons-import', [QuestionLeconController::class,'storeExcel']);
    Route::post('/convert-doc-to-html', [DocumentController::class, 'convertDocumentToHtml']);
    });
});
