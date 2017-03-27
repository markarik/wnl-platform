<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmation extends Mailable
{
	use Queueable, SerializesModels;
	protected $invoice;
	protected $order;

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
			->view('mail.order-confirmation')
			->subject("Potwierdzenie zamówienia #{$this->order->id}")
			->attach($this->invoice->file_path, [
				'as'   => $this->invoice->number_slugged . '.pdf',
				'mime' => 'application/pdf',
			])
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
