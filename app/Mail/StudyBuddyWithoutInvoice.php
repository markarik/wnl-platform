<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudyBuddyWithoutInvoice extends Mailable
{
	use Queueable, SerializesModels;
	public $order;
	public $user;

	/**
	 * Create a new message instance.
	 *
	 * @param Order $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
		$this->user = $order->user;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this
			->view('mail.study-buddy-without-invoice')
			->subject("Twój Study Buddy dołączył właśnie do kursu! (Zamówienie {$this->order->id})")
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
