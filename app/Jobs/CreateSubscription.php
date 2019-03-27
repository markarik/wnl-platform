<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateSubscription
{
	use Dispatchable, InteractsWithQueue, SerializesModels;

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
		\Log::notice("CreateSubscription called for order #{$this->order->id}");
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

		UserSubscription::updateOrCreate(
			['user_id' => $user->id],
			['access_start' => $accessStart, 'access_end' => $accessEnd]
		);

		\Cache::forget(User::getSubscriptionKey($this->order->user->id));
	}
}
