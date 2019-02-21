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
		return $this->respondOk();
	}
}
