<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderInstalment;
use Illuminate\Console\Command;

class MigrateInstalments extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'instalments:migrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate instalments from hard-coded to db';

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
		$orders = Order::where('method', 'instalments')
			->whereIn('product_id', [9, 10])
			->get();

		foreach ($orders as $order) {
			$this->populateInstalments($order);
		}

		return;
	}

	protected function populateInstalments($order)
	{
		$productInstalments = $order->product->instalments;
		$paymentSchedule = $this->generatePaymentSchedule($order);

		foreach ($productInstalments as $i => $productInstalment) {
			$num = $productInstalment->order_number;

			OrderInstalment::create([
				'order_id'     => $order->id,
				'due_date'     => $paymentSchedule[$num]['due_date'],
				'paid'         => $this->isPaid($order, $i),
				'amount'       => $paymentSchedule[$num]['amount'],
				'paid_amount'  => $this->getPaidAmount($order, $i, $paymentSchedule[$num]['amount']),
				'order_number' => $num,
			]);
		}
	}

	protected function isPaid($order, $index)
	{
		if ($order->instalments['allPaid']) {
			return true;
		}

		$instalments = $order->instalments['instalments'];

		return (int)$instalments[$index]['left'] === 0;
	}

	protected function getPaidAmount($order, $index, $fullAmount) {
		if ($order->instalments['allPaid']) {
			return $fullAmount;
		}

		$instalments = $order->instalments['instalments'];

		return $instalments[$index]['amount'] - $instalments[$index]['left'];
	}

	protected function generatePaymentSchedule($order)
	{
		$toDistribute = $order->total_with_coupon;
		$payments = [];

		foreach ($order->product->instalments as $instalment) {
			$num = $instalment->order_number;

			if ($instalment->value_type === 'percentage') {
				$amount = $instalment->value * $toDistribute / 100;
			} else {
				$amount = $instalment->value;
			}

			$toDistribute -= $amount;

			$payments[$num] = [
				'amount'   => $amount,
				'due_date' => $instalment->getDueDate($order),
			];
		}

		return $payments;
	}
}
