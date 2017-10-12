<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Requests\Payment\UseCoupon;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\OrderTransformer;

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

	public function putCoupon(UseCoupon $request)
	{
		$user = Auth::user();
		$orderId = $request->route('id');
		$order = $user->orders()->find($orderId);

		$code = mb_convert_case($request->code, MB_CASE_UPPER, "UTF-8");
		$coupon = Coupon::validCode($code);

		if ($coupon->products->count() > 0 &&
			!$coupon->products->contains($order->product)
		) {
			return $this->respondUnprocessableEntity([
				'errors' => [
					'code' => [
						trans('payment.voucher-product-incompatible'),
					],
				],
			]);
		}

		$order->attachCoupon($coupon);

		return $this->respondOk();
	}
}
