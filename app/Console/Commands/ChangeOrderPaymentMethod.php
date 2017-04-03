<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ChangeOrderPaymentMethod extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:method {id?} {method?}';

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
		$orderId = $this->argument('id');
		$method = $this->argument('method');

		if (!$orderId || !$method) {
			$this->error('Invalid arguments');
			$this->info('Usage: php artisan order:cancel {orderId} {method}');
			exit;
		}

		$order = Order::with(['user', 'product'])->find($orderId);

		if (!$order) {
			$this->error("Sorry, it seems that there is no order with ID {$orderId} in the database.");
			exit;
		}

		$this->info("You're about to change payment method of following order from {$order->method} to {$method}:");
		$this->table(
			['id', 'email', 'name', 'product',],
			[[$order->id, $order->user->email, $order->user->full_name, $order->product->name]]
		);

		if (!$this->confirm("Please confirm")) {
			$this->info('Aborted.');
			exit;
		}

		$order->method = $method;
		$order->save();
		$this->info('Done.');

		return true;
	}
}
