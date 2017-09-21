<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Lib\Invoice\Invoice;

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

		$this->studyBuddyRefund($order);

		return 42;
	}

	protected function studyBuddyRefund($order)
	{
		$studyBuddy = $order->studyBuddy;
		$coupon = $studyBuddy->coupon;

		$recentInvoice = $order
			->invoices()
			->where('series', Invoice::ADVANCE_SERIES_NAME)
			->get()
			->last();
		$reason = 'Zniżka przedzielona po dokonaniu zapłaty (Study Buddy).';
		$difference = $coupon->value * -1;
		(new Invoice)->corrective($order, $recentInvoice, $reason, $difference);

		$order->attachCoupon($coupon);

		$studyBuddy->status = 'refunded';
		$studyBuddy->save();
		$this->info('OK.');

		return 42;
	}
}
