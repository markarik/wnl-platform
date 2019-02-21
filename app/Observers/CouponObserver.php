<?php


namespace App\Observers;


use App\Models\Coupon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Requests;


class CouponObserver
{
	use DispatchesJobs;


	public function created(Coupon $coupon)
	{
		if (empty($coupon->studyBuddy)) {
			// TODO sync the coupon with other platform
			$headers = [
				'Accept' => 'application/json',
				'BETHINK_COUPON_SYNC_TOKEN' => env('APP_COUPONS_SYNC_TOKEN'),
				'Host' => "platform-copy.local"
			];
			$request = Requests::post(env('APP_COUPONS_SYNC_URL') . '/api/v1/coupons', $headers, [
				'coupon' => $coupon->toArray()
			]);
			dd($request->body);
		}
	}

	public function updated(Coupon $coupon) {
		if (empty($coupon->studyBuddy)) {
			// TODO sync the coupon with other platform
		}
	}
}
