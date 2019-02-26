<?php namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('api.resources.coupons');
	}

	public function post(Request $request)
	{
		if ($request->header(Coupon::SYNC_TOKEN_HEADER) !== env('APP_COUPONS_SYNC_TOKEN')) {
			return $this->respondUnauthorized();
		}

		$coupon = new Coupon($request->coupon);
		$coupon->removeObservableEvents(['created']);
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function put(Request $request)
	{
		if ($request->header(Coupon::SYNC_TOKEN_HEADER) !== env('APP_COUPONS_SYNC_TOKEN')) {
			return $this->respondUnauthorized();
		}
		$updatedCoupon = $request->coupon;

		$coupon = Coupon::where('code', $updatedCoupon['code'])->first();

		if (empty($coupon)) {
			return $this->respondNotFound();
		}

		$coupon->times_usable = $updatedCoupon['times_usable'];
		$coupon->removeObservableEvents(['updated']);
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function delete($id)
	{
		if ($this->request->header(Coupon::SYNC_TOKEN_HEADER) !== env('APP_COUPONS_SYNC_TOKEN')) {
			return $this->respondUnauthorized();
		}

		$deletedCoupon = $this->request->coupon;
		$coupon = Coupon::where('code', $deletedCoupon['code'])->first();

		if (empty($coupon)) {
			return $this->respondNotFound();
		}

		$coupon->removeObservableEvents(['deleted']);
		$coupon->delete();
		return $this->respondOk();
	}
}
