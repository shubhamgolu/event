<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $certificatePath;

    public function __construct(Participant $participant, string $certificatePath)
    {
        $this->participant = $participant;
        $this->certificatePath = $certificatePath;
    }

    public function build()
    {
        $mail = $this->subject('Your Certificate: ' . $this->participant->event->name)
                    ->view('emails.certificate')
                    ->with([
                        'participantName' => $this->participant->user->name,
                        'eventName' => $this->participant->event->name,
                        'eventDate' => $this->participant->event->date,
                    ]);
        
        // Attach certificate PDF if it exists
        if (Storage::exists($this->certificatePath)) {
            $mail->attach(Storage::path($this->certificatePath), [
                'as' => 'Certificate.pdf',
                'mime' => 'application/pdf',
            ]);
        }
        
        return $mail;
    }
}