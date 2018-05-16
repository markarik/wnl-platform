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
	protected $signature = 'orders {id?*} {--refund} {--since=} {--potential} {--remindable} {--cancelable} {--instalments}';

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
		$now = new Carbon();

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
			$paidUsers = $orders->where('paid', 1)->pluck('user_id')->toArray();

			$orders = $orders->filter(function ($order) use ($paidUsers) {
				return !$order->paid &&
					!$order->canceled &&
					$order->method !== null &&
					in_array($order->user_id, $paidUsers);
			});
		}

		if ($this->option('remindable')) {
			$orders = $orders->filter(function ($order) use ($now) {
				return $order->method && !$order->paid && !$order->canceled &&
					Carbon::parse($order->created_at)->diffInDays($now) > 7;
			});
		}

		if ($this->option('potential')) {
			$orders = $orders->filter(function ($order) use ($now) {
				return $order->method && !$order->paid && !$order->canceled &&
					Carbon::parse($order->created_at)->diffInDays($now) <= 7;
			});
		}

		if ($this->option('instalments')) {
			$orders = $orders->filter(function ($order) use ($now) {
				if ($order->paid &&
					$order->method === 'instalments' &&
					$order->instalments['allPaid'] === false
				) {
					$firstNotPaid = array_first($order->instalments['instalments'], function ($value, $key) use ($now) {
						return $value['left'] > 0 && $now->gt($value['date']);
					}, false);

					return (bool) $firstNotPaid;
				}

				return false;
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
				$order->paid_at,
				$order->total_with_coupon,
				$order->paid_amount,
				$order->method,
				$order->external_id,
				$order->canceled,
				$order->created_at,
				$order->updated_at,
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
				'paid_at',
				'total',
				'paid_amount',
				'method',
				'p24 ID',
				'canceled',
				'created_at',
				'updated_at',
			],
			$orders
		);

		return true;
	}
}
