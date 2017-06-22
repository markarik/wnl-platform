<?php

namespace App\Console\Commands;

use App\Models\Order;
use Lib\Invoice\Invoice;
use Illuminate\Console\Command;

class IssueFinalInvoice extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoice:final';

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
		$orders = Order::with(['product'])
			->whereDoesntHave('invoices', function ($query) {
				$query->where('series', Invoice::FINAL_SERIES_NAME);
			})
			->where('paid', 1)
			->get();

		foreach ($orders as $order) {
			if ($order->paid_amount === $order->total_with_coupon) {

			}
		}
	}
}
