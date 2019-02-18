<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelOrder extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:cancel {id? : The ID of the order}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Mark give order as canceled';

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
		$orderId = $this->argument('id');

		if (!$orderId) {
			$this->error('Order ID missing!');
			$this->info('Usage: php artisan order:cancel {orderId}');
			exit;
		}

		/** @var Order $order */
		$order = Order::with(['user', 'product'])->find($orderId);

		if (!$order) {
			$this->error("Sorry, it seems that there is no order with ID {$orderId} in the database.");
			exit;
		}

		if ($order->paid) {
			$this->error("Sorry dude, this order has been paid. I can't cancel it. ¯\\_(ツ)_/¯");
			exit;
		}

		$this->info("You're about to cancel following order:");
		$this->table(
			['id', 'email', 'name', 'product',],
			[[$order->id, $order->user->email, $order->user->full_name, $order->product->name]]
		);

		if (!$this->confirm("Please confirm")) {
			$this->info('Aborted.');
			exit;
		}

		$order->cancel();
		$this->info('Done.');

		return true;
	}
}
