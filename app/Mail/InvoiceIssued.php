<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceIssued extends Mailable
{
	use Queueable, SerializesModels;
	protected $invoice;
	protected $order;

	/**
	 * Create a new message instance.
	 *
	 * @param Order $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
		$this->invoice = $order->invoices()->recent();
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this
			->view('mail.invoice-issued')
			->subject("Wystawiliśmy dokument {$this->invoice->full_number} do zamówienia #{$this->order->id}")
			->attach($this->invoice->file_path, [
				'as'   => $this->invoice->number_slugged . '.pdf',
				'mime' => 'application/pdf',
			]);
	}
}
