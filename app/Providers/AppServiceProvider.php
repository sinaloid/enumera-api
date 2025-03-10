<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Notification;
use Laravel\Horizon\Events\JobFailed;
use App\Notifications\FailedJobNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        /*\Illuminate\Support\Facades\Event::listen(JobFailed::class, function ($event) {
            \Log::info('JobFailed event déclenché', ['job_id' => $event->job->getJobId()]);
            Notification::route('mail', 'ounoid@gmail.com') // Remplace par ton email
                ->notify(new FailedJobNotification($event));
        });*/

        \Illuminate\Support\Facades\Event::listen(JobFailed::class, function ($event) {
            \Log::info('JobFailed event déclenché', ['job_id' => $event->job->getJobId()]);
            Notification::route('mail', 'ounoid@gmail.com') // Remplace par ton email
                ->notify(new FailedJobNotification($event));
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                ->notify(new FailedJobTelegramNotification($event->job->getJobId(), $event->exception->getMessage()));
        });
    }
}
