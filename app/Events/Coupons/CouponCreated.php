<?php

namespace App\Events\Coupons;

use App\Models\Coupon;
use App\Traits\SyncCoupons;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Queue\SerializesModels;

class CouponCreated extends CouponEvent {
	use SerializesModels, SyncCoupons;

	public $coupon;

	public function __construct(Coupon $coupon) {
		$this->coupon = $coupon;
	}

	public function shouldSync() {
		return !empty(env('APP_COUPONS_SYNC_SOURCE')) && empty($this->coupon->studyBuddy);
	}

	public function sync() {
		$couponToCreate = $this->coupon->toArray();
		unset($couponToCreate['id']);

		try {
			$this->issueSyncRequest('POST', $couponToCreate);
		} catch (RequestException $exception) {
			// DB::table is used to omit observable
			// The removeObservableEvents doesn't work in this context.
			\DB::table('coupons')->delete($this->coupon->id);
		}
	}
}
