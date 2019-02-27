<?php

namespace App\Events\Coupons;

abstract class CouponEvent {
	abstract public function shouldSync();
	abstract public function sync();
}
