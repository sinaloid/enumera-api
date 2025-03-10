<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class FailedJobNotification extends Notification
{
    use Queueable;

    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['mail', 'log']; // Envoi par email et journalisation
    }

    public function toMail($notifiable)
    {
        \Log::info('Tentative d\'envoi d\'e-mail pour le job échoué', [
            'job_id' => $this->event->job->getJobId(),
            'error' => $this->event->exception->getMessage(),
        ]);
        
        return (new MailMessage)
            ->subject('🚨 Job échoué dans Laravel Horizon')
            ->line('Un job a échoué dans Laravel Horizon.')
            ->line('Queue : ' . $this->event->job->getQueue())
            ->line('Job ID : ' . $this->event->job->getJobId())
            ->line('Erreur : ' . $this->event->exception->getMessage())
            ->action('Voir Horizon', url('/horizon'))
            ->line('Merci de vérifier le problème rapidement.');
    }

    public function toArray($notifiable)
    {
        return [
            'queue' => $this->event->job->getQueue(),
            'job_id' => $this->event->job->getJobId(),
            'error' => $this->event->exception->getMessage(),
        ];
    }
}
