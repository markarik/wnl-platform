<?php

namespace App\Jobs;

use App\Models\Invoice as InvoiceModel;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Lib\Invoice\Invoice;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class OrderPaid implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/** @var Order $order */
	private $order;

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
		$this->updateCoupon();
		$this->unsuspendUser();

		$invoice = $this->getInvoice();
		
		if ($invoice) {
			$this->sendConfirmation($invoice);
		}

		$this->cancelRemainingOrders();
	}

	private function updateCoupon()
	{
		Log::notice("OrderPaid: updateCoupon called for order #{$this->order->id}");
		$order = $this->order;

		if ($order->coupon && $order->coupon->times_usable > 0) {
			$order->coupon->times_usable--;
			$order->coupon->save();
		}
	}

	private function getInvoice(): ?InvoiceModel
	{
		$order = $this->order;

		if ($order->product->delivery_date->isFuture()) {
			Log::notice("OrderPaid: Generating advance invoice for order #{$order->id}");
			return (new Invoice)->advance($order);
		} else if ($this->shouldGenerateVatInvoice($order)) {
			Log::notice("OrderPaid: Generating vat invoice for order #{$order->id}");
			return (new Invoice)->vatInvoice($order);
		} else {
			Log::notice("OrderPaid: Skipping invoice generation for order #{$order->id}");
			return null;
		}
	}

	private function shouldGenerateVatInvoice(Order $order): bool
	{
		$numberOfSuccessfulPayments = $order->payments->where('status', '=', 'success')->count();
		$numberOfVatInvoices = $order->invoices->where('type', '=', 'vat')->count();

		return $numberOfVatInvoices === 0 || $numberOfVatInvoices < $numberOfSuccessfulPayments;
	}

	private function sendConfirmation(InvoiceModel $invoice)
	{
		$order = $this->order;
		Log::notice("OrderPaid: sendConfirmation called for order #{$order->id}");
		Mail::to($order->user)->send(new PaymentConfirmation($order, $invoice));
	}

	private function unsuspendUser()
	{
		Log::notice("OrderPaid: unsuspendUser called for order #{$this->order->id}");
		if ($this->order->method !== 'instalments') return;

		if ($this->order->user->suspended && !$this->order->is_overdue) {
			$this->order->user->suspended = false;
			$this->order->user->save();
		}
	}

	private function cancelRemainingOrders()
	{
		$this->order->user->orders->each(function(Order $order) {
			if (
				!$order->paid
				&& $order->id !== $this->order->id
				&& $order->product->id === $this->order->product->id
			) {
				Log::notice("OrderPaid: canceling order #{$order->id}");
				$order->cancel();
			}
		});
	}
}
