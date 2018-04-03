<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Lib\Invoice\Invoice;
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
		$this->handleCoupon();
		$this->sendConfirmation();
		$this->handleStudyBuddy();
		$this->handleUserSubscription();

	}

	protected function handleCoupon()
	{
		$order = $this->order;

		if ($order->coupon && $order->coupon->times_usable > 0) {
			$order->coupon->times_usable--;
			$order->coupon->save();
		}
	}

	protected function sendConfirmation()
	{
		$order = $this->order;

		\Log::notice('Issuing invoice and sending order confirmation.');

		$invoice = $this->getInvoice($order);

		Mail::to($order->user)->send(new PaymentConfirmation($order, $invoice));
	}

	protected function handleStudyBuddy()
	{
		dispatch(new OrderStudyBuddy($this->order));
	}

	protected function getInvoice($order)
	{
		if ($order->product->delivery_date->isPast()) {
			if ($order->method === 'instalments') {
				return false;
			}

			return (new Invoice)->vatInvoice($order);
		}

		return (new Invoice)->advance($order);
	}

	protected function handleUserSubscription() {
		$product = $this->order->product;
		$user = $this->order->user;

		if ($user->subscription) {
			$accessStart = min([$user->subscription->access_start, $product->access_start]);
			$accessEnd = max([$user->subscription->access_end, $product->access_end]);

			$user->subscription->update([
				'access_start' => $accessStart,
				'access_end' => $accessEnd
			]);
		} else {
			$subscription = new UserSubscription();

			$subscription->access_start = $product->access_start;
			$subscription->access_end = $product->access_end;
			$subscription->user_id = $user->id;

			$subscription->save();
		}
	}
}
