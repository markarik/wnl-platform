<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudyBuddyUsage extends Mailable
{
	use Queueable, SerializesModels;
	public $coupon;
	public $user;
	public $order;

	/**
	 * Create a new message instance.
	 *
	 * @param Order $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
		$this->coupon = $order->studyBuddy->coupon;
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
			->view('mail.study-buddy-usage')
			->subject("Więcej niż LEK - skorzystaj ze zniżki Study Buddy")
			->bcc('zamowienia@wiecejnizlek.pl');
	}
}
