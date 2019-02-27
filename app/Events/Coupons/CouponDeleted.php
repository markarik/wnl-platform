<?php

namespace App\Events\Coupons;

use App\Models\Coupon;
use App\Traits\SyncCoupons;
use Illuminate\Queue\SerializesModels;

class CouponDeleted extends CouponEvent {
	use SerializesModels, SyncCoupons;

	public $coupon;

	public function __construct(Coupon $coupon) {
		$this->coupon = $coupon;
	}

	public function shouldSync() {
		return !empty(env('APP_COUPONS_SYNC_SOURCE')) && empty($this->coupon->studyBuddy);
	}

	public function sync() {
		$this->issueSyncRequest('DELETE', $this->coupon);
	}
}
