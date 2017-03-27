<?php


namespace App\Observers;


use App\Jobs\IssueInvoice;
use App\Jobs\OrderConfirmed;
use App\Jobs\OrderPaid;
use App\Models\Order;
use App\Notifications\OrderCreated;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Notifications\Notifiable;


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
		}
	}

	public function created(Order $order)
	{
		$this->notify(new OrderCreated($order));
	}

	public function routeNotificationForSlack()
	{
		return 'https://hooks.slack.com/services/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';
	}
}
