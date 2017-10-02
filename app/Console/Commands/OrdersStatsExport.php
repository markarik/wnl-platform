<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Google_Service_Sheets_ValueRange;
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
		$setOne = $this->stats(Carbon::parse('2017-04-01 00:00'), Carbon::parse('2017-09-24 23:59'));
		$setTwo = $this->stats(Carbon::parse('2017-09-25 00:00'), Carbon::now());

		$rows = collect();
		foreach ($setOne as $setOneRow) {
			$rows->push(array_merge($setOneRow, $setTwo->shift() ?? []));
		}

		$this->writeRange($rows->toArray());

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
				'',
			]);
		}

		return $rows;
	}

	protected function writeRange($rows)
	{
		$config = config('filesystems.disks.google');
		$client = new \Google_Client();
		$client->setClientId($config['clientId']);
		$client->setClientSecret($config['clientSecret']);
		$client->refreshToken($config['refreshToken']);
		$service = new \Google_Service_Sheets($client);

		$file = env('GOOGLE_SHEETS_ORDERS_STATS');
		$range = 'A:L';
		$body = new Google_Service_Sheets_ValueRange([
			'values' => $rows,
		]);
		$params = [
			'valueInputOption' => 'RAW',
		];
		$service->spreadsheets_values->update($file, $range, $body, $params);
	}
}
