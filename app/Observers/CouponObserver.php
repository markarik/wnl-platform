<?php


namespace App\Observers;


use App\Jobs\SyncCouponUpdate;
use App\Models\Coupon;
use GuzzleHttp\Client;
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

			$client = new Client();
			$client->request('POST', env('APP_COUPONS_SYNC_URL') . "/api/v1/coupons", [
				'headers' => $headers,
				'json' => [
					'coupon' => $couponToCreate
				]
			]);
		}
	}

	public function updated(Coupon $coupon) {
		if (empty($coupon->studyBuddy)) {
			$couponToUpdate = $coupon->toArray();
			unset($couponToUpdate['id']);

			$this->dispatch(new SyncCouponUpdate($couponToUpdate));
		}
	}
}
