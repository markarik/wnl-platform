<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Facades\Lib\Wfirma\Invoice;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceIssued;


/**
 * @property bool mail
 */
class IssueInvoice implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $order;
	protected $proforma;
	protected $send;

	/**
	 * Create a new job instance.
	 *
	 * @param Order $order
	 * @param bool $proforma
	 * @param bool $mail
	 */
	public function __construct(Order $order, $proforma = false, $send = true)
	{
		$this->order = $order;
		$this->proforma = $proforma;
		$this->send = $send;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		Invoice::issueFromOrder($this->order, $this->proforma);
		if ($this->send) {
			Mail::to($this->order->user)->send(new InvoiceIssued($this->order));
		}
	}
}
