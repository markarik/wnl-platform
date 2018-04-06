<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResendInvoice extends Mailable
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
		return $this
			->view('mail.resend-invoice-notification')
			->subject("Poprawiona faktura {$this->invoice->full_number} do zamówienia numer {$this->order->id}")
			->attach($this->invoice->file_path, [
				'as'   => $this->invoice->number_slugged . '.pdf',
				'mime' => 'application/pdf',
			])
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
