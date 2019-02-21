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
		$coupon = new Coupon($request->coupon);
		$coupon->removeObservableEvents(['created']);
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}

	public function put(Request $request)
	{
		$coupon = Coupon::where('code', $request->route('code'))->first();

		if (empty($coupon)) {
			return $this->respondNotFound();
		}

		$coupon->times_usable = $request->coupon->times_usable;
		$coupon->removeObservableEvents(['updated']);
		$coupon->save();
		return $this->transformAndRespond($coupon);
	}
}
