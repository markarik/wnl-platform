<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FinalInvoice extends Mailable implements ShouldQueue
{
	use Queueable, SerializesModels;
	public $order;
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
		$this->invoice = $invoice;
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
			->view('mail.final-invoice-notification')
			->subject("Wystawiliśmy fakturę końcową do Twojego zamówienia numer {$this->order->id}")
			->attachData($invoiceData, $this->invoice->number_slugged . '.pdf', [
				'mime' => 'application/pdf',
			])
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
