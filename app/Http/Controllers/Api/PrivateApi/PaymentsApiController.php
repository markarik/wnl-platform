<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Payment\PostPayment;
use App\Http\Requests\Payment\PostPaymentMethod;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Auth;
use Lib\Przelewy24\Client as P24Client;

class PaymentsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.payments');
	}

	public function post(PostPayment $paymentRequest)
	{
		$user = Auth::user();
		$orderId = $paymentRequest->order_id;
		$amount = $paymentRequest->amount;
		$order = Order::find($orderId);

		if (empty($order)) {
			return $this->respondNotFound();
		}

		$paymentSessionId = str_random(32);

		Payment::create([
			'order_id' => $orderId,
			'status' => 'in-progress',
			'session_id' => $paymentSessionId,
			'amount' => $amount
		]);

		return $this->json([
			'merchant_id' => config('przelewy24.merchant_id'),
			'url_status' => config('przelewy24.status_url'),
			'api_version' => config('przelewy24.api_version'),
			'checksum' => P24Client::generateChecksum(
				$paymentSessionId,
				$amount * 100
			),
			'session_id' => $paymentSessionId,
			'amount' => $amount,
			'user_email' => $user->email,
			'transaction_url' => config('przelewy24.transaction_url'),
		]);
	}

	public function setPaymentMethod(PostPaymentMethod $request)
	{
		$sessionId = $this->request->get('sess_id');
		$paymentMethod = $this->request->get('payment');

		$order = Order::find($request->get('order_id'))->first();

		if (!$order) {
			return response()->json(['status' => 'not_found'], 404);
		}

		$order->method = $paymentMethod;
		$order->save();

		if ($paymentMethod !== Order::PAYMENT_METHOD_INSTALMENTS) {
			$order->orderInstalments()->delete();
		}

		// TODO this payment probably should have some amount - from where should I fetch it?
		Payment::firstOrCreate([
			'session_id' => $sessionId
		], [
			'order_id' => $order->id,
			'status' => 'in-progress',
		]);

		Session::forget(['coupon', 'productId', 'orderId']);

		return response()->json(['status' => 'success']);
	}
}
