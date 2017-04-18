<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Console\Command;

class PopulateAmountColumns extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'payment:populate-amount';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
		$orders = Order::where('paid', 1)->get();

		foreach ($orders as $order) {
			// Perform db query to avoid triggering model events
			\DB::table('orders')
				->where('id', $order->id)
				->update(['paid_amount' => $order->total_with_coupon]);

			$order->invoices()->update(['amount' => $order->total_with_coupon]);
		}
	}
}
