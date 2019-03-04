<?php

namespace App\Jobs;


use App\Events\Coupons\CouponEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SyncCouponUpdate implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable;

	protected $couponEvent;
	protected $coupon;

	public function __construct(CouponEvent $couponEvent, $coupon)
	{
		$this->couponEvent = $couponEvent;
		$this->coupon = $coupon;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->couponEvent->issueSyncRequest('PUT', $this->coupon);
	}
}
