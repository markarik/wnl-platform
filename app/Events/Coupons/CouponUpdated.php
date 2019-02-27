<?php

namespace App\Events\Coupons;

use App\Jobs\SyncCouponUpdate;
use App\Models\Coupon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;

class CouponUpdated implements CouponEvent {
	use SerializesModels, DispatchesJobs;

	public $coupon;

	public function __construct(Coupon $coupon) {
		$this->coupon = $coupon;
	}

	public function shouldSync() {
		return empty($this->coupon->studyBuddy);
	}

	public function sync() {
		$couponToUpdate = $this->coupon->toArray();
		unset($couponToUpdate['id']);

		$this->dispatch(new SyncCouponUpdate($couponToUpdate));
	}
}
