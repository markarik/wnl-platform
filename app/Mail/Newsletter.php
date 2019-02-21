<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Newsletter extends Mailable
{
	use Queueable, SerializesModels;
	protected $email;
	protected $template;

	/**
	 * Create a new message instance.
	 *
	 * @param string $template
	 * @param string $subject
	 * @param string $email
	 */
	public function __construct($template, $subject, $email)
	{
		$this->template = $template;
		$this->subject = $subject;
		$this->email = $email;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this
			->to($this->email)
			->view('mail.newsletter.' . $this->template)
			->text('mail.newsletter.' . $this->template . '-plain')
			->subject($this->subject);
	}
}
