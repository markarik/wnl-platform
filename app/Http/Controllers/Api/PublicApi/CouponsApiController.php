<?php namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.coupons');
	}

	public function post(Request $request)
	{
		if ($request->header('X-BETHINK-COUPON-SYNC-TOKEN') !== env('APP_COUPONS_SYNC_TOKEN')) {
			return $this->respondUnauthorized();
		}

		$coupon = new Coupon($request->coupon);
		$coupon->removeObservableEvents(['created']);
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function put(Request $request)
	{
		if ($request->header('X-BETHINK-COUPON-SYNC-TOKEN') !== env('APP_COUPONS_SYNC_TOKEN')) {
			return $this->respondUnauthorized();
		}

		$coupon = Coupon::where('code', $request->route('code'))->first();

		if (empty($coupon)) {
			return $this->respondNotFound();
		}

		$coupon->times_usable = $request->coupon['times_usable'];
		$coupon->removeObservableEvents(['updated']);
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function delete($code)
	{
		if ($this->request->header('X-BETHINK-COUPON-SYNC-TOKEN') !== env('APP_COUPONS_SYNC_TOKEN')) {
			return $this->respondUnauthorized();
		}

		$coupon = Coupon::where('code', $code);

		if (empty($coupon)) {
			return $this->respondNotFound();
		}

		$coupon->removeObservableEvents(['deleted']);
		$coupon->delete();
		return $this->respondOk();
	}
}
