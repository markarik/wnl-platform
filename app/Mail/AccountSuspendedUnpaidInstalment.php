<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderInstalment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountSuspendedUnpaidInstalment extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

	public $order;

	public $instalment;

	/**
	 * Create a new message instance.
	 *
	 * @param Order $order
	 * @param OrderInstalment $instalment
	 */
    public function __construct(Order $order, OrderInstalment $instalment)
    {
		$this->order = $order;
		$this->instalment = $instalment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		return $this
			->subject("Dostęp do platformy został zawieszony 😔")
			->view('mail.account-suspended-unpaid-instalment')
			->bcc('zamowienia@wiecejnizlek.pl');
    }
}
