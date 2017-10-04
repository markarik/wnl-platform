<?php

namespace App\Jobs;

use App\Mail\StudyBuddy;
use App\Mail\StudyBuddyWithoutInvoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Facades\Lib\Invoice\Invoice;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderPaid implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $order;

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
		$this->sendConfirmation();
		$this->handleStudyBuddy();
	}

	protected function sendConfirmation()
	{
		\Log::notice('Issuing invoice and sending order confirmation.');
		$invoice = Invoice::issueFromOrder($this->order);
		Mail::to($this->order->user)->send(new PaymentConfirmation($this->order, $invoice));
	}

	protected function handleStudyBuddy()
	{
		$order = $this->order;
		// Activate SB code & send email containing code link
		$studyBuddy = $order->studyBuddy;
		if ($studyBuddy && $studyBuddy->status === 'new') {
			\Log::notice('Activating Study Buddy coupon.');
			$studyBuddy->coupon->times_usable++;
			$studyBuddy->coupon->save();
			$studyBuddy->status = 'active';
			$studyBuddy->save();
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
