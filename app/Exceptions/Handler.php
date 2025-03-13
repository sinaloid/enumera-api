<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Notifications\TelegramErrorNotification;
use Illuminate\Support\Facades\Notification;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            
            $chatId = config('services.telegram.chat_id'); // Utilise config() au lieu de env()
            
            // Vérifier si l'application est en production avant d'envoyer la notification
            if (app()->environment('production')) {
                Notification::route('telegram', $chatId)
                    ->notify(new TelegramErrorNotification($e->getMessage()));
            }
        });
    }
}
