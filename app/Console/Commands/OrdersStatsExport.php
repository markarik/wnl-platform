<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Carbon\Carbon;
use Google_Service_Sheets_ValueRange;
use App\Models\Order;

class OrdersStatsExport extends CommandWithMonitoring
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
	protected $description = 'Export orders stats to google doc';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handleCommand()
	{
		$setOne = $this->stats(Carbon::parse('2018-09-24 23:59'), Carbon::now(),
			['date', 'orders_count', 'value', 'paid', 'albums', '50%', '']);

		$setTwo = $this->stats(Carbon::parse('2018-04-02 23:59'), Carbon::parse('2018-09-24 23:59'),
			['date', 'orders_count', 'value', 'paid', '']);

		$setThree = $this->stats(Carbon::parse('2017-09-24 23:59'), Carbon::parse('2018-04-02 23:59'),
			['date', 'orders_count', 'value', 'paid', '']);

		$rows = collect();
		for ($i=1; $i<190; $i++) {
			$rows->push(array_merge(
				$setOne->shift() ?? ['', '', '', '', '', '', ''],
				$setTwo->shift() ?? [],
				$setThree->shift() ?? []
			));
		}

		$this->writeRange($rows->toArray());
	}

	protected function stats($startDate, $endDate, $fields)
	{
		$rows = collect([$fields]);

		$orders = (new Order)
			->with(['coupon', 'product'])
			->where('method', '!=', null)
			->where(function($query){
				$query
					->where('canceled', null)
					->orWhere('canceled', 0);
			})
			->whereBetween('created_at', [$startDate, $endDate])
			->get()
			->unique('user_id');

		$daysDiff = $startDate->diffInDays($endDate) + 1;
		$datePointer = $startDate;
		for ($day = 1; $day <= $daysDiff; $day++) {
			$datePointer->addDay();

			$ordersToDate = $orders->filter(function ($order) use ($datePointer) {
				return $order->created_at < $datePointer;
			});

			$rows->push(
				$this->data($datePointer->format('Y-m-d'), $ordersToDate, $fields)
			);
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
		$range = 'A:Z';
		$body = new Google_Service_Sheets_ValueRange([
			'values' => $rows,
		]);
		$params = [
			'valueInputOption' => 'RAW',
		];
		$service->spreadsheets_values->update($file, $range, $body, $params);
	}

	protected function data($date, $orders, $fields)
	{
		$data = [
			'date'         => $date,
			'orders_count' => $orders->count(),
			'value'        => $orders->sum('total_with_coupon'),
			'paid'         => $orders->sum('paid_amount'),
			'coupons'      => $orders->where('coupon', true)->count(),
			'albums'       => $orders->whereNotIn('coupon.slug', [Coupon::SLUG_WNL_ONLINE_ONLY])->count(),
			'50%'          => $orders->where('coupon.slug', Coupon::SLUG_WNL_ONLINE_ONLY)->count(),
			''             => '',
		];

		return array_values(array_intersect_key($data, array_flip($fields)));
	}
}
