<?php

namespace App\Jobs;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Lib\Invoice\Invoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderConfirmed implements ShouldQueue
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
		$invoice = (new Invoice)->proforma($this->order);
		Mail::to($this->order->user)->send(new OrderConfirmation($this->order, $invoice));
	}
}
