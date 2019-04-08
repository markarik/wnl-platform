<?php

namespace App\Listeners;

use App\Events\Coupons\CouponEvent;

class PerformCouponSync
{
	public function handle(CouponEvent $event)
	{
		if ($event->shouldSync($event->coupon)) {
			$event->sync();
		}
	}
}
