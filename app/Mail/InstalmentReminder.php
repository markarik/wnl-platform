<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderInstalment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InstalmentReminder extends Mailable implements ShouldQueue
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
			->subject("Zbliża się termin płatności kolejnej raty (Zamówienie {$this->order->id})")
			->view('mail.payment-reminder-instalment')
			->bcc('zamowienia@wiecejnizlek.pl');
    }
}
