<?php


namespace App\Observers;


use App\Models\Order;
use App\Jobs\OrderPaid;
use App\Jobs\OrderConfirmed;
use Illuminate\Support\Facades\App;
use App\Notifications\OrderCreated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Bus\DispatchesJobs;


class OrderObserver
{
	use DispatchesJobs, Notifiable;

	public function updated(Order $order)
	{
		if ($order->isDirty(['paid_amount']) && $order->paid_amount > $order->getOriginal('paid_amount')) {
			\Log::notice('Order paid, dispatching OrderPaid job.');
			$this->dispatch(new OrderPaid($order));
		}

		if (!$order->paid &&
			$order->getOriginal('method') === null &&
			$order->method !== null
		) {
			$this->handlePaymentMethodSet($order);
		}

		if ($order->getOriginal('coupon_id') !== $order->coupon_id) {
			$this->handleCouponChange($order);
		}
	}

	public function created(Order $order)
	{

	}

	public function routeNotificationForSlack()
	{
		if (App::environment('production')) {
			return env('SLACK_ORDERS_URL');
		} else {
			return env('SLACK_TEST');
		}
	}

	protected function handlePaymentMethodSet($order)
	{
		\Log::notice('Order payment method set, decrementing product quantity.');
		$this->dispatch(new OrderConfirmed($order));
		$order->product->quantity--;
		$order->product->save();

		if ($order->coupon && $order->coupon->times_usable > 0) {
			$order->coupon->times_usable--;
			$order->coupon->save();
		}

		if (intval($order->total_with_coupon) === 0) {
			\Log::notice('Order total is 0, marking as paid and dispatching OrderPaid job.');
			$order->paid = true;
			$order->save();
			$this->dispatch(new OrderPaid($order));
		}

		$this->notify(new OrderCreated($order));
	}

	protected function handleCouponChange($order)
	{
		\Log::notice('Order coupon changed.');
		if ($order->studyBuddy) {
			$order->studyBuddy->delete();
		}
	}
}
