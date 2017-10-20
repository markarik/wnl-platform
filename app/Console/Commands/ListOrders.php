<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ListOrders extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders {id?*} {--refund} {--since=} {--remindable} {--cancelable}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all orders';

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

		if (empty ($orderId)) {
			$orders = Order::with(['user', 'product'])->get();
		} else {
			$orders = Order::with(['user', 'product'])->whereIn('id', $orderId)->get();

			if (!$orders) {
				$this->error("Sorry, it seems that there is no order with ID {$orderId} in the database.");
				exit;
			}
		}

		if ($this->option('since')) {
			$orders = $orders->filter(function ($order) {
				return $order->created_at > Carbon::parse($this->option('since'));
			});
		}

		if ($this->option('refund')) {
			$orders = $orders->filter(function ($order) {
				return $order->paid_amount > $order->total_with_coupon;
			});
		}

		if ($this->option('cancelable')) {
			$paidUsers = array_values($orders->filter(function ($order) {
				return $order->paid > 0;
			})->map(function ($order) {
				return $order->user_id;
			})->toArray());

			$orders = $orders->filter(function ($order) use ($paidUsers) {
				return !$order->paid &&
					!$order->canceled &&
					$order->method !== null &&
					in_array($order->user_id, $paidUsers);
			});
		}

		if ($this->option('remindable')) {
			$now = new Carbon();
			$orders = $orders->filter(function ($order) use ($now) {
				return $order->method && !$order->paid && !$order->canceled &&
					Carbon::parse($order->created_at)->diffInDays($now) > 7;
			});
		}

		$orders = $orders->map(function ($order) {
			return [
				$order->id,
				$order->user_id,
				$order->user->email ?? '-',
				$order->user->full_name ?? '-',
				$order->product->name,
				$order->paid,
				$order->total_with_coupon,
				$order->paid_amount,
				$order->method,
				$order->external_id,
				$order->canceled,
				$order->created_at,
			];
		})->toArray();

		$this->table(
			[
				'order id',
				'user ID',
				'user email',
				'user name',
				'product',
				'paid',
				'total',
				'paid_amount',
				'method',
				'p24 ID',
				'canceled',
				'created_at',
			],
			$orders
		);

		return true;
	}
}
