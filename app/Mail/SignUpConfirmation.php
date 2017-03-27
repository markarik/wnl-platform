<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SignUpConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

	/**
	 * Create a new message instance.
	 *
	 */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
			->view('mail.sign-up-confirmation')
			->subject('Potwierdzenie rejestracji');
    }
}
