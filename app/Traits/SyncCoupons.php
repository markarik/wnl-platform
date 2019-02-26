<?php

namespace App\Traits;

use App\Models\Coupon;
use GuzzleHttp\Client;

trait SyncCoupons {
	protected function issueSyncRequest($method, $body) {
		$headers = [
			'Accept' => 'application/json',
			'Host' => env('APP_COUPONS_SYNC_HOST'),
			Coupon::SYNC_TOKEN_HEADER => env('APP_COUPONS_SYNC_TOKEN'),
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
