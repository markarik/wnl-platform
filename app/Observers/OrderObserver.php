<?php


namespace App\Observers;


use App\Jobs\IssueInvoice;
use App\Models\Order;
use Illuminate\Foundation\Bus\DispatchesJobs;


class OrderObserver
{
	use DispatchesJobs;

	public function updated(Order $order)
	{
		if ($order->isDirty(['paid']) && $order->paid) {
			$this->dispatch(new IssueInvoice($order));
		}
	}

	public function created(Order $order)
	{
		$this->dispatch(new IssueInvoice($order, $proforma = true));
	}
}
