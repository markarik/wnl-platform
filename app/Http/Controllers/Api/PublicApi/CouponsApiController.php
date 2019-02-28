<?php namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Coupon\SyncCoupon;
use App\Models\Coupon;

class CouponsApiController extends ApiController
{
	public function __construct(SyncCoupon $request)
	{
		parent::__construct($request);
		$this->resourceName = config('api.resources.coupons');
	}

	public function post(SyncCoupon $request)
	{
		$coupon = new Coupon($request->coupon);
		// This API is created to perform coupon's sync
		// We don't want to dispatch events to prevent infinite loop
		$coupon->unsetEventDispatcher();
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function put(SyncCoupon $request)
	{
		$updatedCoupon = $request->coupon;

		$coupons = Coupon::where('code', $updatedCoupon['code'])->get();

		if ($coupons->count() < 1) {
			return $this->respondNotFound();
		}

		if ($coupons->count() > 1) {
			\Log::warning('More than one coupon with the same code. Do not know which to update');
		}

		$coupon = $coupons->first();
		$coupon->times_usable = $updatedCoupon['times_usable'];
		// This API is created to perform coupon's sync
		// We don't want to dispatch events to prevent infinite loop
		$coupon->unsetEventDispatcher();
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function deleteCoupon(SyncCoupon $request)
	{
		$deletedCoupon = $this->request->coupon;
		$coupons = Coupon::where('code', $deletedCoupon['code'])->get();

		if ($coupons->count() < 1) {
			return $this->respondNotFound();
		}

		if ($coupons->count() > 1) {
			\Log::warning('More than one coupon with the same code. Do not know which to delete');
			return $this->respondInvalidInput();
		}

		$coupon = $coupons->first();
		// This API is created to perform coupon's sync
		// We don't want to dispatch events to prevent infinite loop
		$coupon->unsetEventDispatcher();
		$coupon->delete();
		return $this->respondOk();
	}
}
