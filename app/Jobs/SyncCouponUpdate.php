<?php

namespace App\Jobs;


use App\Traits\SyncCoupons;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SyncCouponUpdate implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SyncCoupons;

	protected $coupon;

	public function __construct($coupon)
	{
		$this->coupon = $coupon;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->issueSyncRequest('PUT', $this->coupon);
	}
}
