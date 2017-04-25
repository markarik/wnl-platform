<?php


namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\OrderTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class OrdersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.orders');
	}

	public function get($id)
	{
		$user = Auth::user();

		if ($id === 'all') {
			$orders = $user->orders;
			$resource = new Collection($orders, new OrderTransformer, $this->resourceName);
		} else {
			$order = $user->orders()->find($id);
			$resource = new Item($order, new OrderTransformer, $this->resourceName);
		}

		$data = $this->fractal->createData($resource)->toArray();

		return response()->json($data);
	}
}
