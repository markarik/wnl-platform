<?php

namespace App\Events\Coupons;

interface CouponEvent {
	public function shouldSync();
	public function sync();
}
