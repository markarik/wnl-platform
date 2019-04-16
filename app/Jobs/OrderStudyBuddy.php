<?php

namespace App\Jobs;

use App\Mail\StudyBuddyUsage;
use Carbon\Carbon;
use Mail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
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

	/** @var Order $order */
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

		// Generate SB and send email containing code link
		if (empty($order->coupon) && $order->product->slug !== Product::SLUG_WNL_ALBUM) {
			$this->generateStudyBuddy($order);
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

	protected function generateStudyBuddy(Order $order)
	{
		\Log::notice("Generating Study Buddy for order #$order->id.");

		$expires = Carbon::now()->addYears(1);
		$coupon = new Coupon([
			'name' => 'Study Buddy',
			'type' => 'amount',
			'value' => 100,
			'expires_at' => $expires,
			'code' => strtoupper(str_random(7)),
			'times_usable' => 1,
			'kind' => Coupon::KIND_STUDY_BUDDY,
		]);

		$order->studyBuddy()->create([
			'code' => $coupon->code,
			'status' => 'active'
		]);

		$coupon->save();
		$coupon->products()->attach(
			Product::whereIn('slug', [Product::SLUG_WNL_ONLINE])->get()
		);
	}
}
