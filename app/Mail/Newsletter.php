<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Newsletter extends Mailable
{
	use Queueable, SerializesModels;
	protected $template;

	/**
	 * Create a new message instance.
	 *
	 * @param $template
	 * @param $subject
	 */
	public function __construct($template, $subject)
	{
		$this->template = $template;
		$this->subject = $subject;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this
			->to('newsletter@mail.wiecejnizlek.pl')
			->view('mail.newsletter.' . $this->template)
			->subject($this->subject);
	}
}
