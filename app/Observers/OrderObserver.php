<?php


namespace App\Observers;


use App\Jobs\IssueInvoice;
use App\Jobs\OrderConfirmed;
use App\Jobs\OrderPaid;
use App\Models\Order;
use Illuminate\Foundation\Bus\DispatchesJobs;


class OrderObserver
{
	use DispatchesJobs;

	public function updated(Order $order)
	{
		if ($order->isDirty(['paid']) && $order->paid) {
			$this->dispatch(new OrderPaid($order));
		}

		if ($order->getOriginal('method') === null) {
			$this->dispatch(new OrderConfirmed($order));
		}
	}

	public function created(Order $order)
	{

	}
}
