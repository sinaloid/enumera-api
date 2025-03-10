<?php

use Illuminate\Support\Facades\Route;
use Laravel\Horizon\Horizon;

use App\Notifications\FailedJobTelegramNotification;
use Illuminate\Support\Facades\Notification;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/kkk', function () {
    return view('welcome');
});


Horizon::auth(function ($request) {
    return true; // Remplace par une logique d'authentification si nécessaire
});


use Illuminate\Support\Facades\Log;

Route::get('/fail-job', function () {
    dispatch(function () {
        throw new Exception("Ce job est volontairement en échec !");
    })->onQueue('high');

    Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
    ->notify(new FailedJobTelegramNotification('test-job-id', 'Test error message'));

    return "Job échoué ajouté à la queue.";
});

use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    Mail::raw('Ceci est un test d’email.', function ($message) {
        $message->to('ounoid@gmail.com')
                ->subject('Test d’email Laravel');
    });

    return "Email envoyé ! Vérifie ta boîte de réception.";
});