<?php

namespace App\Mail;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudyGroup extends Mailable
{
    use Queueable, SerializesModels;

	public $coupon;

	/**
	 * Create a new message instance.
	 *
	 * @param Coupon $coupon
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
	        ->subject('Twój kod Study Group właśnie zajechał')
	        ->view('mail.study-group');
    }
}
