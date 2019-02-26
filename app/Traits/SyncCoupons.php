<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait SyncCoupons {

	protected function issueSyncRequest($method, $body) {
		$headers = [
			'Accept' => 'application/json',
			'Host' => env('APP_COUPONS_SYNC_HOST'),
			'X-BETHINK-COUPON-SYNC-TOKEN' => env('APP_COUPONS_SYNC_TOKEN'),
		];

		$client = new Client();
		$client->request($method, env('APP_COUPONS_SYNC_URL') . "/api/v1/coupons", [
			'headers' => $headers,
			'json' => [
				'coupon' => $body
			]
		]);
	}
}
