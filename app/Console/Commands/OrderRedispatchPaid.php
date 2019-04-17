<?php

namespace App\Console\Commands;

use App\Jobs\OrderPaid;
use App\Models\Order;
use Illuminate\Console\Command;

class OrderRedispatchPaid extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:redispatchPaid {orders}';

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
		foreach (explode(',', $this->argument('orders')) as $order) {
			$this->info('Redispatching OrderPaid for ' . $order);
			dispatch(new OrderPaid(Order::find($order), true));
		}

		return;
	}
}
