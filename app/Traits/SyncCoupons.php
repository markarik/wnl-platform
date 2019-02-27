<?php

namespace App\Traits;

use Facades\App\Contracts\Requests;
use App\Models\Coupon;

trait SyncCoupons {
	protected function issueSyncRequest($method, $coupon) {
		$headers = [
			'Accept' => 'application/json',
			'Host' => config('coupons.coupons_sync_host'),
			Coupon::SYNC_TOKEN_HEADER => config('coupons.coupons_sync_token'),
		];

		$url = config('coupons.coupons_sync_url') . "/api/v1/coupons";
		$body = [
			'coupon' => $coupon
		];

		Requests::request($method, $url, $body, $headers);
	}
}
