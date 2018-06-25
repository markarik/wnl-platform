<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Refund extends Mailable implements ShouldQueue
{
	use Queueable, SerializesModels;
	public $order, $user, $invoice, $value;

	/**
	 * Create a new message instance.
	 *
	 * @param Order $order
	 * @param Invoice $invoice
	 * @param int $invoice
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
		$invoiceData = \Storage::get($this->invoice->file_path);

		return $this
			->view('mail.refund')
			->subject("Wykonaliśmy zwrot na Twoje konto! (Zamówienie {$this->order->id})")
			->attachData($invoiceData, $this->invoice->number_slugged . '.pdf', [
				'mime' => 'application/pdf',
			])
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
