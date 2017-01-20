<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserSignedUp extends Mailable
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
			->view('mail.user_signed_up')
			->subject('Potwierdzenie rejestracji');
    }
}
