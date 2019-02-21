<?php namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class CouponsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.coupons');
	}

	public function post(Request $request)
	{
		return $this->respondOk();
	}

	public function put(Request $request)
	{
		return $this->respondOk();
	}
}
