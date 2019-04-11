<?php namespace App\Http\Controllers\Api\PrivateApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class OrdersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.orders');
	}
}
