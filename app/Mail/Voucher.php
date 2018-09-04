<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Voucher extends Mailable
{
    use Queueable, SerializesModels;

	public $coupon;

	/**
	 * Create a new message instance.
	 *
	 * @param $coupon
	 */
    public function __construct($coupon)
    {
	    $this->coupon = $coupon;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
	        ->subject('Zajechał kupon')
	        ->view('mail.voucher');
    }
}
