<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendinviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $input;

    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($input, $email)
    {
        $this->input = $input;
        $this->email = $email;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $subject = 'Invitation Received';

        return $this->subject($subject)
            ->markdown('emails.sendinvite_mail')
            ->with($this->input);
    }
}
