<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Lib\Invoice\Invoice;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderPaid implements ShouldQueue
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
		$this->handleCoupon();
		$this->handleInstalments();
		$this->sendConfirmation();
		$this->cancelRemainingOrders();
	}

	protected function handleCoupon()
	{
		\Log::notice("OrderPaid: handleCoupon called for order #{$this->order->id}");
		$order = $this->order;

		if ($order->coupon && $order->coupon->times_usable > 0) {
			$order->coupon->times_usable--;
			$order->coupon->save();
		}
	}

	protected function sendConfirmation()
	{
		\Log::notice("OrderPaid: sendConfirmation called for order #{$this->order->id}");
		$order = $this->order;

		\Log::debug('Issuing invoice and sending order confirmation.');

		$invoice = $this->getInvoice($order);

		Mail::to($order->user)->send(new PaymentConfirmation($order, $invoice));
	}

	protected function getInvoice($order)
	{
		if ($order->product->delivery_date->isPast()) {
			return (new Invoice)->vatInvoice($order);
		}

		return (new Invoice)->advance($order);
	}

	protected function handleInstalments()
	{
		\Log::notice("OrderPaid: handleInstalments called for order #{$this->order->id}");
		if ($this->order->method !== 'instalments') return;

		$this->order->generateAndSavePaymentSchedule();

		if ($this->order->user->suspended && !$this->order->is_overdue) {
			$this->order->user->suspended = false;
			$this->order->user->save();
		}
	}

	private function cancelRemainingOrders()
	{
		$this->order->user->orders->each(function(Order $order) {
			if (!$order->paid && $order->id !== $this->order->id) {
				$order->cancel();
			}
		});
	}
}
