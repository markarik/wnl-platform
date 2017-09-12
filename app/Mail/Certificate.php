<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Storage;

class Certificate extends Mailable
{
	use Queueable, SerializesModels;
	public $user;
	protected $file;

	/**
	 * Create a new message instance.
	 *
	 * @param $file
	 */
	public function __construct($file, $user)
	{
		$this->file = Storage::disk('s3')->get($file);
		$this->user = $user;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$firstName = title_case($this->user->first_name);
		$lastName = title_case($this->user->last_name);
		$fileName = "Certyfikat_{$firstName}_{$lastName}.pdf";

		return $this
			->view('mail.certificate')
			->subject('Certyfikat uczestnictwa - Więcej niż LEK')
			->attachData($this->file, $fileName, [
				'mime' => 'application/pdf',
			])
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
