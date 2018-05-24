<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class OrdersShippingStatus extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders:shipping
		{status : A new status for orders shipping (new, ordered, in_progress, or delivered)}
		{ids : Comma separated list of ids of orders to affect}
	';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Change shipping status for given orders';

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
		$status = $this->argument('status');
		$ordersIds = $this->argument('ids');

		if (!$ordersIds || !$status) {
			$this->error('Order ID or status missing!');
			$this->info('Usage: php artisan orders:shipping -S {status} -I {IDs}');
			exit;
		}

		$idsArray = explode(',', $ordersIds);
		$orders = Order::with(['user', 'product'])
			->whereIn('id', $idsArray);

		if (!$orders->count()) {
			$this->error("Sorry, it seems that there are no orders with given IDs in the database.");
			exit;
		}

		if (!$this->confirm("You are about to change a shipping status to `{$status}` for " . count($idsArray) . " orders. Is it right?")) {
			$this->info('Ok, nevermind. Aborted.');
			exit;
		}

		$orders->update(['shipping_status' => $status]);
		$this->info('Done.');

		return true;
	}
}
