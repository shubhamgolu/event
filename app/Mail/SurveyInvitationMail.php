<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $surveyUrl;

    public function __construct(Participant $participant, string $surveyUrl)
    {
        $this->participant = $participant;
        $this->surveyUrl = $surveyUrl;
    }

    public function build()
    {
        return $this->subject('Survey Invitation: ' . $this->participant->event->name)
                    ->view('emails.survey-invitation')
                    ->with([
                        'participantName' => $this->participant->user->name,
                        'eventName' => $this->participant->event->name,
                        'eventDate' => $this->participant->event->date,
                        'surveyUrl' => $this->surveyUrl,
                    ]);
    }
}