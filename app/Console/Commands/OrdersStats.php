<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class OrdersStats extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders:stats';

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
		$headers = ['Order ID', 'From', 'Product', 'Coupon', 'Total'];
		$rows = collect();

		$orders =
			(new Order)
				->with(['coupon', 'product'])
				->where('id', '>', 470) // :)
				->where('method', '!=', null)
				->get();

		$rows = $orders->map(function ($order) {
			return [
				$order->id,
				$order->user->full_name,
				$order->product->name,
				str_limit($order->coupon->name ?? '', 20),
				$order->total_with_coupon,
			];
		})->toArray();

		$this->table($headers, $rows);

		$ordersCount = $orders->count();
		$total = $orders->sum('total_with_coupon');
		$paid = $orders->sum('paid_amount');
		$coupons = $orders->where('coupon', true)->count();
		$online = $orders->where('product.slug', 'wnl-online')->count();
		$onsite = $orders->where('product.slug', 'wnl-online-onsite')->count();
		$coupons50 = $orders->where('coupon.slug', 'wnl-online-only')->count();
		$couponsSb = $orders->where('coupon.name', 'Study Buddy')->count();

		$headers = ['Orders', 'Total value', 'Paid amount', 'Coupons', '50% Coupons', 'Study Buddy', 'Online', 'Onsite'];
		$rows = [[$ordersCount, $total, $paid, $coupons, $coupons50, $couponsSb, $online, $onsite]];

		$this->table($headers, $rows);
	}
}
