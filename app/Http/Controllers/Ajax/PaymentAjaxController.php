<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentAjaxController extends Controller
{
	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function setPaymentMethod()
	{
		$sessionId = $this->request->get('sess_id');
		$method = $this->request->get('payment');

		$order = Order::where(['session_id' => $sessionId])->first();

		if (!$order) {
			return response()->json(['status' => 'not_found'], 404);
		}

		$order->method = $method;
		$order->save();

		// user can click submit button more than once, the new payment shouldn't be created in such case
		if (!Payment::where('session_id', $sessionId)->count()) {
			// TODO this payment probably should have some amount - from where should I fetch it?
			Payment::create([
				'order_id' => $order->id,
				'status' => 'in-progress',
				'session_id' => $sessionId
			]);
		}

		return response()->json(['status' => 'success']);
	}
}
