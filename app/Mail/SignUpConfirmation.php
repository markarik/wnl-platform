<?php

namespace App\Mail;

use App\Models\User;
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
	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	* Build the message.
	*
	* @return $this
	*/
	public function build()
	{
		return $this
			->view('mail.sign-up-confirmation', ['user' => $this->user])
			->subject('Potwierdzenie rejestracji');
	}
}
