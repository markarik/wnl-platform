<?php

namespace App\Traits;

use Facades\App\Contracts\Requests;

trait SyncCoupons {
	protected function issueSyncRequest($method, $coupon) {
		$headers = [
			'Accept' => 'application/json',
			'Host' => config('coupons.coupons_sync_host'),
			config('coupons.coupons_sync_header') => config('coupons.coupons_sync_token'),
		];

		$url = config('coupons.coupons_sync_url') . "/api/v1/coupons";
		$body = [
			'coupon' => $coupon
		];

		Requests::request($method, $url, $body, $headers);
	}
}
