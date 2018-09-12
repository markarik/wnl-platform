<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Payment\PostPayment;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
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
		$order = Order::find($orderId);
		if (empty($order)) {
			return $this->respondNotFound();
		}

		$paymentSessionId = str_random(32);
		//TODO this should handled differently to support instalments
		$amount = (int)$order->total_with_coupon * 100;

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
				$amount
			),
			'session_id' => $paymentSessionId,
			'amount' => $amount,
			'user_email' => $user->email,
			'transaction_url' => config('przelewy24.transaction_url'),
		]);
	}
}
