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
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\EvaluationLeconController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionLeconController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Gate;

// Définir les politiques de contrôle d'accès (si nécessaire)
// Exemple: Gate::define('create-role', 'App\Policies\RolePolicy@create');

// Route API
Route::group(['middleware' => ['cors','json.response']], function () {
    Route::post('/register', [AuthController::class,'register']);
    Route::post('/verify-otp', [AuthController::class,'verifyOtp']);
    Route::post('/get-otp', [AuthController::class,'getOtp']);
    Route::post('/login', [AuthController::class,'login']);
    Route::post('/edit-password', [AuthController::class,'editPassword']);
    Route::get('/classes/public', [ClasseController::class,'index']);

    Route::middleware(['auth:api'])->group(function () {
        Route::get('/users', [AuthController::class,'index'])->middleware('can:view user');
        Route::get('/users/auth', [AuthController::class,'userAuth']);
        Route::post('/users/update', [AuthController::class,'update'])->middleware('can:update user');
        Route::post('/users/changePassword', [AuthController::class,'changePassword']);
        Route::post('/users/get', [AuthController::class,'userBy'])->middleware('can:view user');
        Route::post('/users/disable', [AuthController::class,'disable'])->middleware('can:delete user');

        Route::post('/users/change-active-statut', [AuthController::class,'changeActiveStatus']);
        Route::post('/users/change-block-statut', [AuthController::class,'changeBlockStatus']);
        Route::get('/users/profile/{profile}', [AuthController::class,'getUserByProfile'])->middleware('can:view user');

        Route::get('/classes/{slug}/matieres', [ClasseController::class,'getClasseMatiere'])->middleware('can:view classe');
        Route::get('/classes/{classe}/matieres/{matiere}/chapitres', [ClasseController::class,'getClasseMatiereChapitres'])->middleware('can:view classe');

        Route::get('/matiere-de-la-classe/classe/{slug}', [MatiereDeLaClasseController::class,'getMatiereDeLaClasseByClasseSlug'])->middleware('can:view matiereDeLaclasse');
        Route::get('/utilisateurs/profile/{slug}', [UtilisateurController::class,'getUtilisateurByProfile'])->middleware('can:view user');
        Route::get('/evaluations-lecons/lecon/{slug}', [EvaluationLeconController::class,'getEvaluationByLeconSlug'])->middleware('can:view evaluationLecon');

        /** Matières */
        Route::get('/matieres/classe/{slugClasse}', [MatiereController::class,'getMatiereByClasse'])->middleware('can:view matiere');
        Route::get('/matieres/classe/{slugClasse}/periode/{slugPeriode}', [MatiereController::class,'getMatiereByClassePeriode'])->middleware('can:view matiere');

        /** Chapitres */
        Route::get('/chapitres/classe/{slugClasse}', [ChapitreController::class,'getChapitreByClasse'])->middleware('can:view chapitre');
        Route::get('/chapitres/classe/{slugClasse}/matiere/{slugMatiere}', [ChapitreController::class,'getChapitreByClasseMatiere'])->middleware('can:view chapitre');
        Route::get('/chapitres/classe/{slugClasse}/periode/{slugPeriode}/matiere/{slugMatiere}', [ChapitreController::class,'getChapitreByClassePeriodeMatiere'])->middleware('can:view chapitre');
        Route::post('/chapitres/import', [ChapitreController::class,'importChapitre'])->middleware('can:create chapitre');

        /** Leçons */
        Route::get('/lecons/chapitre/{slug}', [LeconController::class,'getLeconByChapitreSlug'])->middleware('can:view lecon');
        Route::get('/lecons/periode/{slugPeriode}', [LeconController::class,'getLeconByPeriode'])->middleware('can:view lecon');
        Route::get('/lecons/classe/{slugClasse}', [LeconController::class,'getLeconByClasse'])->middleware('can:view lecon');
        Route::get('/lecons/classe/{slugClasse}/periode/{slugPeriode}', [LeconController::class,'getLeconByClassePeriode'])->middleware('can:view lecon');
        Route::get('/lecons/classe/{slugClasse}/periode/{slugPeriode}/matiere/{slugMatiere}', [LeconController::class,'getLeconByClassePeriodeMatiere'])->middleware('can:view lecon');
        Route::get('/lecons/classe/{slugClasse}/periode/{slugPeriode}/matiere/{slugMatiere}/chapitre/{slugChapitre}', [LeconController::class,'getLeconByClassePeriodeMatiereChapitre'])->middleware('can:view lecon');

        Route::get('/chapitres/classe/{slugClasse}/periode/{slugPeriode}', [ChapitreController::class,'getChapitreByPeriodeClasse'])->middleware('can:view chapitre');

        /** Evaluations */
        Route::get('/evaluations/classe/{slugClasse}', [EvaluationController::class,'getEvaluationByClasse'])->middleware('can:view evaluation');

        /** Question */
        Route::get('/questions/evaluation/{slugEvaluation}', [QuestionController::class,'getQuestionByEvaluation'])->middleware('can:view question');
        /** Question Leçons */
        Route::get('/questions-lecons/evaluation/{slugEvaluation}', [QuestionLeconController::class,'getQuestionByEvaluation'])->middleware('can:view questionLecon');

        Route::middleware(['auth:api'])->group(function () {

            // Utilisateurs Routes
            Route::get('/utilisateurs', [UtilisateurController::class, 'index'])->middleware('can:view user');
            Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'show'])->middleware('can:view user');
            Route::post('/utilisateurs', [UtilisateurController::class, 'store'])->middleware('can:create user');
            Route::put('/utilisateurs/{id}', [UtilisateurController::class, 'update'])->middleware('can:update user');
            Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy'])->middleware('can:delete user');

            // Classes Routes
            Route::get('/classes', [ClasseController::class, 'index'])->middleware('can:view classe');
            Route::get('/classes/{id}', [ClasseController::class, 'show'])->middleware('can:view classe');
            Route::post('/classes', [ClasseController::class, 'store'])->middleware('can:create classe');
            Route::put('/classes/{id}', [ClasseController::class, 'update'])->middleware('can:update classe');
            Route::delete('/classes/{id}', [ClasseController::class, 'destroy'])->middleware('can:delete classe');

            // Matières Routes
            Route::get('/matieres', [MatiereController::class, 'index'])->middleware('can:view matiere');
            Route::get('/matieres/{id}', [MatiereController::class, 'show'])->middleware('can:view matiere');
            Route::post('/matieres', [MatiereController::class, 'store'])->middleware('can:create matiere');
            Route::put('/matieres/{id}', [MatiereController::class, 'update'])->middleware('can:update matiere');
            Route::delete('/matieres/{id}', [MatiereController::class, 'destroy'])->middleware('can:delete matiere');

            // Périodes Routes
            Route::get('/periodes', [PeriodeController::class, 'index'])->middleware('can:view periode');
            Route::get('/periodes/{id}', [PeriodeController::class, 'show'])->middleware('can:view periode');
            Route::post('/periodes', [PeriodeController::class, 'store'])->middleware('can:create periode');
            Route::put('/periodes/{id}', [PeriodeController::class, 'update'])->middleware('can:update periode');
            Route::delete('/periodes/{id}', [PeriodeController::class, 'destroy'])->middleware('can:delete periode');

            // Matière de la Classe Routes
            Route::get('/matiere-de-la-classe', [MatiereDeLaClasseController::class, 'index'])->middleware('can:view matiereDeLaClasse');
            Route::get('/matiere-de-la-classe/{id}', [MatiereDeLaClasseController::class, 'show'])->middleware('can:view matiereDeLaClasse');
            Route::post('/matiere-de-la-classe', [MatiereDeLaClasseController::class, 'store'])->middleware('can:create matiereDeLaClasse');
            Route::put('/matiere-de-la-classe/{id}', [MatiereDeLaClasseController::class, 'update'])->middleware('can:update matiereDeLaClasse');
            Route::delete('/matiere-de-la-classe/{id}', [MatiereDeLaClasseController::class, 'destroy'])->middleware('can:delete matiereDeLaClasse');

            // Chapitres Routes
            Route::get('/chapitres', [ChapitreController::class, 'index'])->middleware('can:view chapitre');
            Route::get('/chapitres/{id}', [ChapitreController::class, 'show'])->middleware('can:view chapitre');
            Route::post('/chapitres', [ChapitreController::class, 'store'])->middleware('can:create chapitre');
            Route::put('/chapitres/{id}', [ChapitreController::class, 'update'])->middleware('can:update chapitre');
            Route::delete('/chapitres/{id}', [ChapitreController::class, 'destroy'])->middleware('can:delete chapitre');

            // Leçons Routes
            Route::get('/lecons', [LeconController::class, 'index'])->middleware('can:view lecon');
            Route::get('/lecons/{id}', [LeconController::class, 'show'])->middleware('can:view lecon');
            Route::post('/lecons', [LeconController::class, 'store'])->middleware('can:create lecon');
            Route::put('/lecons/{id}', [LeconController::class, 'update'])->middleware('can:update lecon');
            Route::delete('/lecons/{id}', [LeconController::class, 'destroy'])->middleware('can:delete lecon');

            // Cours Routes
            Route::get('/cours', [CoursController::class, 'index'])->middleware('can:view cours');
            Route::get('/cours/{id}', [CoursController::class, 'show'])->middleware('can:view cours');
            Route::post('/cours', [CoursController::class, 'store'])->middleware('can:create cours');
            Route::put('/cours/{id}', [CoursController::class, 'update'])->middleware('can:update cours');
            Route::delete('/cours/{id}', [CoursController::class, 'destroy'])->middleware('can:delete cours');

            // Evaluations Routes
            Route::get('/evaluations', [EvaluationController::class, 'index'])->middleware('can:view evaluation');
            Route::get('/evaluations/{id}', [EvaluationController::class, 'show'])->middleware('can:view evaluation');
            Route::post('/evaluations', [EvaluationController::class, 'store'])->middleware('can:create evaluation');
            Route::put('/evaluations/{id}', [EvaluationController::class, 'update'])->middleware('can:update evaluation');
            Route::delete('/evaluations/{id}', [EvaluationController::class, 'destroy'])->middleware('can:delete evaluation');

            // Evaluations-Leçons Routes
            Route::get('/evaluations-lecons', [EvaluationLeconController::class, 'index'])->middleware('can:view evaluationLecon');
            Route::get('/evaluations-lecons/{id}', [EvaluationLeconController::class, 'show'])->middleware('can:view evaluationLecon');
            Route::post('/evaluations-lecons', [EvaluationLeconController::class, 'store'])->middleware('can:create evaluationLecon');
            Route::put('/evaluations-lecons/{id}', [EvaluationLeconController::class, 'update'])->middleware('can:update evaluationLecon');
            Route::delete('/evaluations-lecons/{id}', [EvaluationLeconController::class, 'destroy'])->middleware('can:delete evaluationLecon');

            // Questions Routes
            Route::get('/questions', [QuestionController::class, 'index'])->middleware('can:view question');
            Route::get('/questions/{id}', [QuestionController::class, 'show'])->middleware('can:view question');
            Route::post('/questions', [QuestionController::class, 'store'])->middleware('can:create question');
            Route::put('/questions/{id}', [QuestionController::class, 'update'])->middleware('can:update question');
            Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->middleware('can:delete question');

            // Questions-Leçons Routes
            Route::get('/questions-lecons', [QuestionLeconController::class, 'index'])->middleware('can:view questionLecon');
            Route::get('/questions-lecons/{id}', [QuestionLeconController::class, 'show'])->middleware('can:view questionLecon');
            Route::post('/questions-lecons', [QuestionLeconController::class, 'store'])->middleware('can:create questionLecon');
            Route::put('/questions-lecons/{id}', [QuestionLeconController::class, 'update'])->middleware('can:update questionLecon');
            Route::delete('/questions-lecons/{id}', [QuestionLeconController::class, 'destroy'])->middleware('can:delete questionLecon');

            // ChatGPT Routes
            Route::get('/chatgpt', [ChatController::class, 'index'])->middleware('can:view chatgpt');
            Route::get('/chatgpt/{id}', [ChatController::class, 'show'])->middleware('can:view chatgpt');
            Route::post('/chatgpt', [ChatController::class, 'store'])->middleware('can:create chatgpt');
            Route::put('/chatgpt/{id}', [ChatController::class, 'update'])->middleware('can:update chatgpt');
            Route::delete('/chatgpt/{id}', [ChatController::class, 'destroy'])->middleware('can:delete chatgpt');

            // Routes supplémentaires
            Route::middleware(['can:view file'])->group(function () {
                Route::get('/files', [LeconController::class, 'getFile'])->name('files.index');
                Route::get('/files/lecon/{slug}', [LeconController::class, 'getLeconFile'])->name('files.filesLecon');
                Route::post('/files', [LeconController::class, 'storeFile'])->name('files.store');
                Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
            });

            Route::middleware(['can:create questionLecon'])->group(function () {
                Route::post('/questions-lecons-import', [QuestionLeconController::class, 'storeExcel']);
            });

            Route::middleware(['can:create question'])->group(function () {
                Route::post('/questions-import', [QuestionController::class, 'storeExcel']);
            });

            Route::middleware(['can:create document'])->group(function () {
                Route::post('/convert-doc-to-html', [DocumentController::class, 'convertDocumentToHtml']);
            });

            // Routes de gestion des rôles et permissions (accessible uniquement aux super-admins et admins)
            Route::get('/permissions', [PermissionController::class, 'index'])->middleware('can:view permission');
            Route::get('/permissions/{id}', [PermissionController::class, 'show'])->middleware('can:view permission');
            Route::post('/permissions', [PermissionController::class, 'store'])->middleware('can:create permission');
            Route::put('/permissions/{id}', [PermissionController::class, 'update'])->middleware('can:update permission');
            Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->middleware('can:delete permission');

            Route::get('/roles', [RoleController::class, 'index'])->middleware('can:view role');
            Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('can:view role');
            Route::post('/roles', [RoleController::class, 'store'])->middleware('can:create role');
            Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('can:update role');
            Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('can:delete role');
            
            /*Route::middleware(['role:super-admin|admin'])->group(function() {

            });*/
        });
    });
});
