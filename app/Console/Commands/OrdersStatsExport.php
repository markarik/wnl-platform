<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Storage;
use App\Models\Order;
use Illuminate\Console\Command;

class OrdersStatsExport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders:statsExport';

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
		$storage = Storage::disk('local');
		$rootDir = 'exports';

		$setOne = $this->stats(Carbon::parse('2017-04-01 00:00'), Carbon::parse('2017-09-24 23:59'));
		$setTwo = $this->stats(Carbon::parse('2017-09-25 00:00'), Carbon::now());

		$rows = collect();
		foreach ($setOne as $setOneRow) {
			$rows->push(implode("\t", array_merge($setOneRow, $setTwo->shift() ?? [])));
		}

		$storage->put($rootDir . '/Stats.tsv', $rows->implode("\n"));

		return 42;
	}

	protected function stats($startDate, $endDate)
	{
		$rows = collect([['date', 'orders_count', 'value', 'paid', 'coupons', '']]);

		$orders = (new Order)
			->with(['coupon', 'product'])
			->where('method', '!=', null)
			->whereBetween('created_at', [$startDate, $endDate])
			->get();

		$daysDiff = $startDate->diffInDays($endDate);
		$datePointer = $startDate;
		for ($day = 1; $day <= $daysDiff; $day++) {
			$datePointer->addDay();
			$ordersToDate = $orders->filter(function ($order) use ($datePointer) {
				return $order->created_at < $datePointer;
			});
			$rows->push([
				$datePointer->format('Y-m-d'),
				$ordersToDate->count(),
				$ordersToDate->sum('total_with_coupon'),
				$ordersToDate->sum('paid_amount'),
				$ordersToDate->where('coupon', true)->count(),
				''
			]);
		}

		return $rows;
	}
}
