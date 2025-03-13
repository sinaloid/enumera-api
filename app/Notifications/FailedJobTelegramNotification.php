<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class FailedJobTelegramNotification extends Notification
{
    use Queueable;

    protected $jobId;
    protected $error;

    public function __construct($jobId, $error)
    {
        $this->jobId = $jobId;
        $this->error = $error;
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $chatId = config('services.telegram.chat_id'); // Utilise config() au lieu de env()

        if (empty($chatId)) {
            \Log::error('Chat ID Telegram non défini. Vérifiez .env et config/services.php.');
            return;
        }
        //dd(config('services.telegram.chat_id'));

        return TelegramMessage::create()
            ->to($chatId)
            ->content("🚨 *Job Failed Notification* 🚨\n\n"
                . "📌 *Job ID:* `{$this->jobId}`\n"
                . "❌ *Error:* `{$this->error}`\n"
                . "📅 *Date:* " . now()->format('d/m/Y H:i:s'));
    }
}
