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
		if ($request->header(Coupon::SYNC_TOKEN_HEADER) !== config('coupons.coupons_sync_token')) {
			return $this->respondUnauthorized();
		}

		$coupon = new Coupon($request->coupon);
		$coupon->unsetEventDispatcher();
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function put(Request $request)
	{
		if ($request->header(Coupon::SYNC_TOKEN_HEADER) !== config('coupons.coupons_sync_token')) {
			return $this->respondUnauthorized();
		}
		$updatedCoupon = $request->coupon;

		$coupon = Coupon::where('code', $updatedCoupon['code'])->first();

		if (empty($coupon)) {
			return $this->respondNotFound();
		}

		$coupon->times_usable = $updatedCoupon['times_usable'];
		$coupon->unsetEventDispatcher();
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function deleteCoupon()
	{
		if ($this->request->header(Coupon::SYNC_TOKEN_HEADER) !== config('coupons.coupons_sync_token')) {
			return $this->respondUnauthorized();
		}

		$deletedCoupon = $this->request->coupon;
		$coupon = Coupon::where('code', $deletedCoupon['code'])->first();

		if (empty($coupon)) {
			return $this->respondNotFound();
		}

		$coupon->unsetEventDispatcher();
		$coupon->delete();
		return $this->respondOk();
	}
}
