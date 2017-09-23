<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StudyBuddy extends Mailable
{
	use Queueable, SerializesModels;
	public $order;
	public $user;
	protected $invoice;

	/**
	 * Create a new message instance.
	 *
	 * @param Order $order
	 * @param Invoice $invoice
	 */
	public function __construct(Order $order, Invoice $invoice)
	{
		$this->order = $order;
		$this->user = $order->user;
		$this->invoice = $invoice;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this
			->view('mail.study-buddy')
			->subject("Twój Study Buddy dołączył właśnie do kursu!")
			->attach($this->invoice->file_path, [
				'as'   => $this->invoice->number_slugged . '.pdf',
				'mime' => 'application/pdf',
			])
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
