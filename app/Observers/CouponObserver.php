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
		if (empty(env('APP_COUPONS_SYNC_SOURCE'))) {
			return;
		}

		if (empty($coupon->studyBuddy)) {
			$couponToCreate = $coupon->toArray();
			unset($couponToCreate['id']);

			$headers = [
				'Accept' => 'application/json',
				'Host' => env('APP_COUPONS_SYNC_HOST'),
				'X-BETHINK-COUPON-SYNC-TOKEN' => env('APP_COUPONS_SYNC_TOKEN'),
			];
			Requests::post(env('APP_COUPONS_SYNC_URL') . '/api/v1/coupons', $headers, [
				'coupon' => $couponToCreate
			]);
		}
	}

	public function updated(Coupon $coupon) {
		if (empty($coupon->studyBuddy)) {
			$couponToUpdate = $coupon->toArray();
			unset($couponToUpdate['id']);

			$headers = [
				'Accept' => 'application/json',
				'Host' => env('APP_COUPONS_SYNC_HOST'),
				'X-BETHINK-COUPON-SYNC-TOKEN' => env('APP_COUPONS_SYNC_TOKEN'),
			];
			Requests::put(env('APP_COUPONS_SYNC_URL') . "/api/v1/coupons/{$coupon->code}", $headers, [
				'coupon' => $couponToUpdate
			]);
		}
	}
}
