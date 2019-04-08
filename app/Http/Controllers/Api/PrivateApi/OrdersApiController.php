<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Requests\Payment\UseCoupon;
use App\Jobs\OrderPaid;
use App\Jobs\OrderStudyBuddy;
use App\Models\Coupon;
use App\Models\Order;
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
			$order = Order::find($id);

			if (!$user->can('view', $order)) {
				return $this->respondForbidden();
			}

			$resource = new Item($order, new OrderTransformer, $this->resourceName);
		}

		$data = $this->fractal->createData($resource)->toArray();

		return response()->json($data);
	}

	public function putCoupon(UseCoupon $request)
	{
		$user = Auth::user();
		$orderId = $request->route('id');
		/** @var Order $order */
		$order = $user->orders()->find($orderId);

		$code = mb_convert_case($request->code, MB_CASE_UPPER, "UTF-8");
		$coupon = Coupon::validCode($code);

		$errors = $this->validateCoupon($order, $coupon);

		if (!empty($errors)) {
			return $this->respondUnprocessableEntity([
				'errors' => [
					'code' => $errors,
				],
			]);
		}

		if ($coupon->studyBuddy) {
			dispatch(new OrderStudyBuddy($order));
		}
		$order->attachCoupon($coupon);

		if (intval($order->total_with_coupon) === 0) {
			$order->paid = true;
			$order->save();
			$this->dispatch(new OrderPaid($order));
		}

		if ($order->paid && $coupon->times_usable > 0) {
			$coupon->times_usable--;
			$coupon->save();
		}

		return $this->respondOk();
	}

	/**
	 * Additional coupon validation, that couldn't be done
	 * in custom request class.
	 *
	 * @param Order $order
	 * @param Coupon $coupon
	 * @return array
	 */
	public function validateCoupon(Order $order, Coupon $coupon)
	{
		$errors = [];

		if (!$coupon->isApplicableForProduct($order->product)) {
			array_push($errors, trans('payment.voucher-product-incompatible'));
		}

		if ($order->studyBuddy &&
			$order->studyBuddy->code === $coupon->code
		) {
			array_push($errors, trans('payment.study-buddy-self-application'));
		}

		if ($order->product->signups_end->isPast()) {
			array_push($errors, trans('payment.product-coupon-too-late'));
		}

		return $errors;
	}

	public function cancel($id)
	{
		$order = Order::find($id);

		if (!$order) {
			return $this->respondNotFound();
		}

		if (!\Auth::user()->can('cancel', $order)) {
			return $this->respondForbidden();
		}

		$order->cancel();

		return $this->respondOk($this->transform($order));
	}
}
