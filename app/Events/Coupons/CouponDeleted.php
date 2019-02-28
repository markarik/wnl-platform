<?php

namespace App\Events\Coupons;

use App\Models\Coupon;
use Illuminate\Queue\SerializesModels;

class CouponDeleted extends CouponEvent {
	use SerializesModels;

	public $coupon;

	public function __construct(Coupon $coupon) {
		$this->coupon = $coupon;
	}

	public function shouldSync() {
		return !empty(config('coupons.coupons_sync_is_source')) && empty($this->coupon->studyBuddy);
	}

	public function sync() {
		$this->issueSyncRequest('DELETE', $this->coupon->toArray());
	}
}
