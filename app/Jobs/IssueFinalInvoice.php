<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Facades\Lib\Invoice\Invoice;
use Illuminate\Support\Facades\Mail;
use App\Mail\FinalInvoice;


class IssueFinalInvoice implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $order;
	protected $proforma;
	protected $send;

	/**
	 * Create a new job instance.
	 *
	 * @param Order $order
	 * @param bool $send
	 *
	 * @internal param bool $mail
	 */
	public function __construct(Order $order, $send = true)
	{
		$this->order = $order;
		$this->send = $send;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$invoice = Invoice::finalInvoice($this->order);
		if ($this->send) {
			Mail::to($this->order->user)->send(new FinalInvoice($this->order, $invoice));
		}
	}
}
