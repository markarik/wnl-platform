<?php

namespace App\Events\Coupons;

use App\Jobs\SyncCouponUpdate;
use App\Models\Coupon;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CouponUpdated extends CouponEvent
{
	use DispatchesJobs;

	public $coupon;

	public function __construct(Coupon $coupon)
	{
		$this->coupon = $coupon;
	}

	public function sync()
	{
		$couponToUpdate = $this->coupon->toArray();
		unset($couponToUpdate['id']);

		$this->dispatch(new SyncCouponUpdate($this, $couponToUpdate));
	}
}
