<?php

namespace App\Events\Coupons;

use App\Models\Coupon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Queue\SerializesModels;

class CouponCreated extends CouponEvent
{
	use SerializesModels;

	public $coupon;

	public function __construct(Coupon $coupon)
	{
		$this->coupon = $coupon;
	}

	public function shouldSync(Coupon $coupon)
	{
		return !empty(config('coupons.coupons_sync_is_source')) && $this->isCouponSyncable($coupon);
	}

	public function sync()
	{
		$couponToCreate = $this->coupon->toArray();
		unset($couponToCreate['id']);

		try {
			$this->issueSyncRequest('POST', $couponToCreate);
		} catch (RequestException $exception) {
			// DB::table is used to omit observable
			// The removeObservableEvents doesn't work in this context.
			\DB::table('coupons')->delete($this->coupon->id);
			throw $exception;
		}
	}
}
