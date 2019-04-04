<?php

namespace App\Jobs;

use App\Mail\StudyBuddyUsage;
use Mail;
use App\Models\Order;
use App\Mail\StudyBuddy;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Mail\StudyBuddyWithoutInvoice;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderStudyBuddy implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	public $order;

	/**
	 * Create a new job instance.
	 *
	 * @param Order $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$order = $this->order;
		// Activate SB code & send email containing code link
		$studyBuddy = $order->studyBuddy;
		if (empty($order->coupon) && $studyBuddy && $studyBuddy->status === 'new') {
			\Log::notice('Activating Study Buddy coupon.');
			$studyBuddy->coupon->times_usable++;
			$studyBuddy->coupon->save();
			$studyBuddy->status = 'active';
			$studyBuddy->save();
			Mail::to($order->user)->send(new StudyBuddyUsage($order));
		}

		// Check if order has SB code and handle the refund
		if ($order->coupon &&
			$order->coupon->studyBuddy &&
			$order->coupon->studyBuddy->status === 'active'
		) {
			\Log::notice('Study Buddy coupon used - awaiting refund.');
			$originalOrder = Order::find($order->coupon->studyBuddy->order_id);

			if ($originalOrder->total_with_coupon > $originalOrder->paid_amount) {
				$originalOrder->attachCoupon($order->coupon);
				$order->coupon->studyBuddy->status = 'refunded';
				$order->coupon->times_usable = 0;
				Mail::to($originalOrder->user)->send(new StudyBuddyWithoutInvoice($originalOrder));
			} else {
				$order->coupon->studyBuddy->status = 'awaiting-refund';
				Mail::to($originalOrder->user)->send(new StudyBuddy($originalOrder));
			}

			$order->coupon->save();
			$order->coupon->studyBuddy->save();
		}
	}
}
