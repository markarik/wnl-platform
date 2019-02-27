<?php namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('api.resources.coupons');
	}

	public function post(Request $request)
	{
		if ($request->header(config('coupons.coupons_sync_header')) !== config('coupons.coupons_sync_token')) {
			return $this->respondUnauthorized();
		}

		$coupon = new Coupon($request->coupon);
		$coupon->unsetEventDispatcher();
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function put(Request $request)
	{
		if ($request->header(config('coupons.coupons_sync_header')) !== config('coupons.coupons_sync_token')) {
			return $this->respondUnauthorized();
		}
		$updatedCoupon = $request->coupon;

		$coupons = Coupon::where('code', $updatedCoupon['code'])->get();

		if ($coupons->count() < 1) {
			return $this->respondNotFound();
		}

		if ($coupons->count() > 1) {
			\Log::warn('More than one coupon with the same code. Do not know what to update');
		}

		$coupon = $coupons->first();
		$coupon->times_usable = $updatedCoupon['times_usable'];
		$coupon->unsetEventDispatcher();
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function deleteCoupon()
	{
		if ($this->request->header(config('coupons.coupons_sync_header')) !== config('coupons.coupons_sync_token')) {
			return $this->respondUnauthorized();
		}

		$deletedCoupon = $this->request->coupon;
		$coupons = Coupon::where('code', $deletedCoupon['code'])->get();

		if ($coupons->count() < 1) {
			return $this->respondNotFound();
		}

		if ($coupons->count() > 1) {
			\Log::warn('More than one coupon with the same code. Do not know which to delete');
		}

		$coupon = $coupons->first();
		$coupon->unsetEventDispatcher();
		$coupon->delete();
		return $this->respondOk();
	}
}
