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
		if ($order->isDirty(['paid']) && $order->paid) {
			$this->dispatch(new OrderPaid($order));
		}

		if ($order->getOriginal('method') === null && $order->method !== null) {
			$this->dispatch(new OrderConfirmed($order));
			$order->product->quantity--;
			$order->product->save();
		}
	}

	public function created(Order $order)
	{
		if (App::environment('production')) {
			$this->notify(new OrderCreated($order));
		}
	}

	public function routeNotificationForSlack()
	{
		return env('SLACK_ORDERS_URL');
	}
}
