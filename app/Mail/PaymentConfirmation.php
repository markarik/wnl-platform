<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentConfirmation extends Mailable implements ShouldQueue
{
	use Queueable, SerializesModels;
	public $order;
	public $invoice;

	/**
	 * Create a new message instance.
	 *
	 * @param Order $order
	 * @param Invoice $invoice
	 */
	public function __construct(Order $order, $invoice)
	{
		$this->order = $order;
		$this->invoice = $invoice;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$this
			->view('mail.payment-confirmation')
			->subject("Otrzymaliśmy wpłatę za kurs! (zamówienie numer {$this->order->id})")
			->bcc('zamowienia@wiecejnizlek.pl');

		if ($this->invoice) {
			$invoiceData = \Storage::drive()->get($this->invoice->file_path);

			$this->attachData($invoiceData, $this->invoice->number_slugged . '.pdf', [
				'mime' => 'application/pdf',
			]);
		}

		return $this;
	}
}
