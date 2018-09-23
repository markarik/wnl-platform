<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class MarkOrderAsPaid extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:paid {id? : The ID of the order}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Mark order as paid';

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

		if (!$orderId) {
			$this->error('Order ID missing!');
			$this->info('Usage: php artisan order:cancel {orderId}');
			exit;
		}

		$order = Order::with(['user', 'product'])->find($orderId);

		if (!$order) {
			$this->error("Sorry, it seems that there is no order with ID {$orderId} in the database.");
			exit;
		}

		$this->info("You're about to mark following order as paid:");
		$this->table(
			['id', 'email', 'name', 'product', 'method', 'paid_amount'],
			[[
				$order->id,
				$order->user->email,
				$order->user->full_name,
				$order->product->name,
				$order->method,
				$order->paid_amount,
			]]
		);

		if (!$this->confirm("Please confirm")) {
			$this->info('Aborted.');
			exit;
		}

		$amount = $this->ask("Enter amount from transfer (already paid: {$order->paid_amount})", $order->total_with_coupon);

		$order->paid_amount += $amount;
		$order->save();
		$this->info('Done.');

		return true;
	}
}
