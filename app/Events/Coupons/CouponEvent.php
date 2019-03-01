<?php

namespace App\Events\Coupons;

use Facades\App\Contracts\Requests;

abstract class CouponEvent {
	public $coupon;

	abstract public function shouldSync();
	abstract public function sync();

	public function issueSyncRequest($method, $coupon) {
		if (empty(config('coupons.coupons_sync_url'))) {
			\Log::info('Can not sync coupon. The sync URL is not provided');
			return;
		}

		$headers = [
			'Accept' => 'application/json',
			config('coupons.coupons_sync_header') => config('coupons.coupons_sync_token'),
		];

		$url = config('coupons.coupons_sync_url') . "/api/v1/coupons";
		$body = [
			'coupon' => $coupon
		];

		Requests::request($method, $url, $headers, $body);
	}
}
