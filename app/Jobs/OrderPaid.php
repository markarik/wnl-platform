<?php

namespace App\Jobs;

use App\Http\Controllers\Api\PrivateApi\EditionsApiController;
use App\Models\Order;
use App\Models\User;
use App\Models\UserLesson;
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
		$this->handleUserSubscription();
		$this->handleUserLessons();
		$this->handleCoupon();
		$this->handleInstalments();
		$this->sendConfirmation();

		EditionsApiController::clearCache($this->order->user->id);
		\Cache::forget(User::getSubscriptionKey($this->order->user->id));
	}

	protected function handleCoupon()
	{
		\Log::notice("OrderPaid: handleCoupon called for order #{$this->order->id}");
		$order = $this->order;

		if ($order->coupon && $order->coupon->times_usable > 0) {
			$order->coupon->times_usable--;
			$order->coupon->save();
		}
	}

	protected function sendConfirmation()
	{
		\Log::notice("OrderPaid: sendConfirmation called for order #{$this->order->id}");
		$order = $this->order;

		\Log::debug('Issuing invoice and sending order confirmation.');

		$invoice = $this->getInvoice($order);

		Mail::to($order->user)->send(new PaymentConfirmation($order, $invoice));
	}

	protected function getInvoice($order)
	{
		if ($order->product->delivery_date->isPast()) {
			return (new Invoice)->vatInvoice($order);
		}

		return (new Invoice)->advance($order);
	}

	protected function handleUserSubscription()
	{
		\Log::notice("OrderPaid: handleUserSubscription called for order #{$this->order->id}");
		$product = $this->order->product;
		$user = $this->order->user;

		if (empty($product->access_start) && empty($product->access_end)) {
			return;
		}

		$subscriptionAccessStart = $user->subscription->access_start ?? null;
		$subscriptionAccessEnd = $user->subscription->access_end ?? null;

		$accessStart = $subscriptionAccessStart
			? min([$subscriptionAccessStart, $product->access_start])
			: $product->access_start;
		$accessEnd = max([$subscriptionAccessEnd, $product->access_end]);

		$subscription = UserSubscription::updateOrCreate(
			['user_id' => $user->id],
			['access_start' => $accessStart, 'access_end' => $accessEnd]
		);
	}

	protected function handleUserLessons()
	{
		\Log::notice("OrderPaid: handleUserLessons called for order #{$this->order->id}");
		$lessons = $this->order->product->lessons;
		$user = $this->order->user;

		$lessonsWithStartDate = $lessons->map(function ($item) use ($user) {
			if ($item->isAccessible($user)) {
				return null;
			}

			return [
				'lesson_id'  => $item->id,
				'start_date' => $item->pivot->start_date,
				'user_id'    => $user->id,
			];
		})->filter()->toArray();

		UserLesson::insert($lessonsWithStartDate);
	}

	protected function handleInstalments()
	{
		\Log::notice("OrderPaid: handleInstalments called for order #{$this->order->id}");
		if ($this->order->method !== 'instalments') return;

		$this->order->generatePaymentSchedule();

		if ($this->order->user->suspended && !$this->order->is_overdue) {
			$this->order->user->suspended = false;
			$this->order->user->save();
		}
	}
}
