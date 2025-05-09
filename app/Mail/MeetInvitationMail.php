<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MeetInvitationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $participant;
    public $meet;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($participant, $meet)
    {
        $this->participant = $participant;
        $this->meet = $meet;
    }

    public function build()
    {
        return $this->subject("Invitation à la session : {$this->meet->titre}")
                    ->view('mails.meet_invitation');
    }
}
