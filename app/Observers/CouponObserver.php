<?php


namespace App\Observers;


use App\Jobs\SyncCouponUpdate;
use App\Models\Coupon;
use App\Traits\SyncCoupons;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Bus\DispatchesJobs;


class CouponObserver
{
	use DispatchesJobs, SyncCoupons;

	public function created(Coupon $coupon)
	{
		if (empty(env('APP_COUPONS_SYNC_SOURCE'))) {
			return;
		}

		if (empty($coupon->studyBuddy)) {
			$couponToCreate = $coupon->toArray();
			unset($couponToCreate['id']);

			try {
				$this->issueSyncRequest('POST', $couponToCreate);
			} catch (RequestException $exception) {
				// DB::table is used to omit observable
				// The removeObservableEvents doesn't work in this context.
				\DB::table('coupons')->delete($coupon->id);
			}
		}
	}

	public function updated(Coupon $coupon) {
		if (empty($coupon->studyBuddy)) {
			$couponToUpdate = $coupon->toArray();
			unset($couponToUpdate['id']);

			$this->dispatch(new SyncCouponUpdate($couponToUpdate));
		}
	}

	public function deleted(Coupon $coupon) {
		if (empty(env('APP_COUPONS_SYNC_SOURCE'))) {
			return;
		}

		if (empty($coupon->studyBuddy)) {
			$this->issueSyncRequest('DELETE', $coupon);
		}
	}
}
