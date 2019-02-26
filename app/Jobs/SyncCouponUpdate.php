<?php

namespace App\Jobs;


use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SyncCouponUpdate implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable;

	protected $coupon;

	public function __construct($coupon)
	{
		$this->coupon = $coupon;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function handle()
	{
		$headers = [
			'Accept' => 'application/json',
			'Host' => env('APP_COUPONS_SYNC_HOST'),
			'X-BETHINK-COUPON-SYNC-TOKEN' => env('APP_COUPONS_SYNC_TOKEN'),
		];

		$client = new Client();
		$client->request('PUT', env('APP_COUPONS_SYNC_URL') . "/api/v1/coupons/{$this->coupon['code']}", [
			'headers' => $headers,
			'json' => [
				'coupon' => $this->coupon
			]
		]);
	}
}
