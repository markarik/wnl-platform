<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CorrectiveInvoice extends Mailable
{
	use Queueable, SerializesModels;
	public $order, $user, $invoice, $value;

	/**
	 * Create a new message instance.
	 *
	 * @param Order $order
	 * @param Invoice $invoice
	 * @param $value
	 */
	public function __construct(Order $order, Invoice $invoice, $value)
	{
		$this->order = $order;
		$this->user = $order->user;
		$this->invoice = $invoice;
		$this->value = $value;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this
			->view('mail.corrective-invoice')
			->subject("Wystawiliśmy fakturę korygującą (Zamówienie {$this->order->id})")
			->attach($this->invoice->file_path, [
				'as'   => $this->invoice->number_slugged . '.pdf',
				'mime' => 'application/pdf',
			])
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
