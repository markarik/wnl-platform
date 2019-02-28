<?php

namespace App\Console\Commands;

use App\Mail\Refund;
use App\Models\Order;
use Illuminate\Console\Command;
use Lib\Invoice\Invoice;
use Mail;

class StudyBuddyRefund extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sb:refund {order}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$orderId = $this->argument('order');
		$order = Order::find($orderId);

		if (!$order) {
			$this->warn('Order doesn\'t exist.');

			return 42;
		}

		if (!$order->studyBuddy) {
			$this->warn('No Study Buddy attached to this order!');

			return 42;
		}

		if (count($order->invoices()->get()) > 2 &&
			!$this->confirm('This order has suspiciously many invoices... Do you want to continue?')
		) {
			$this->info('Aborted.');
			exit;
		}

		$this->studyBuddyRefund($order);
	}

	protected function studyBuddyRefund($order)
	{
		$this->info("Do you want to refund this order:");
		$this->table(
			['id', 'email', 'name', 'product',],
			[[$order->id, $order->user->email, $order->user->full_name, $order->product->name]]
		);

		if (!$this->confirm("Please confirm")) {
			$this->info('Aborted.');
			exit;
		}

		$studyBuddy = $order->studyBuddy;
		$coupon = $studyBuddy->coupon;

		$recentInvoice = $order
			->invoices()
			->whereIn('series', [config('invoice.advance_series'), config('invoice.vat_series')])
			->get()
			->last();
		$reason = 'Zniżka przydzielona po dokonaniu zapłaty (Study Buddy).';
		$value = $coupon->value;
		$difference = $value * -1;

		$order->attachCoupon($coupon);
		$order->paid_amount = $order->paid_amount + $difference;
		$order->save();

		$coupon->times_usable = 0;
		$coupon->save();

		$studyBuddy->status = 'refunded';
		$studyBuddy->save();

		$invoice = (new Invoice)->corrective($order, $recentInvoice, $reason, $difference);

		Mail::to($order->user)->send(new Refund($order, $invoice, $value));

		$this->info('OK. Invoice number: ' . $invoice->full_number);
	}
}
