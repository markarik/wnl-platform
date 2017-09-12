<?php


namespace App\Observers;


use App\Models\Order;
use App\Jobs\OrderPaid;
use App\Jobs\IssueInvoice;
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

		if ($order->getOriginal('method') === null && $order->method !== null) {
			\Log::notice('Order payment method set, decrementing product quantity.');
			$this->dispatch(new OrderConfirmed($order));
			$order->product->quantity--;
			$order->product->save();

			if ($order->coupon && $order->coupon->times_usable) {
				$order->coupon->times_usable--;
				$order->coupon->save();
			}

			$this->notify(new OrderCreated($order));
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
}
