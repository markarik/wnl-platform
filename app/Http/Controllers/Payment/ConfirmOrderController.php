<?php

namespace App\Http\Controllers\Payment;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Lib\Przelewy24\Client as Payment;
use Illuminate\Support\Facades\Auth;

class ConfirmOrderController extends Controller
{
	public function index(Payment $payment)
	{

		$user = Auth::user();

		if (!$user) {
			return redirect(route('payment-select-product'));
		}

		$order = $user->orders()->recent();

		$checksum = $payment::generateChecksum($order->session_id, (int)$order->product->price * 100);

		return view('payment.confirm-order', [
			'order'    => $order,
			'user'     => $user,
			'checksum' => $checksum,
		]);
	}

	public function handle(Request $request)
	{
		$user = Auth::user();
		$order = $user->orders()->recent();
		$order->method = $request->input('method');
		$order->save();

		return redirect(route('profile-orders'));
	}

	public function status(Request $request, Payment $payment)
	{
		//TODO: IP filtering
		Log::debug('request:' . json_encode($request->request->all(), JSON_PRETTY_PRINT));
		Log::debug('headers:' . json_encode($request->headers->all(), JSON_PRETTY_PRINT));

		$transactionValid = $payment->verifyTransaction(
			$request->get('p24_session_id'),
			$request->get('p24_amount'),
			$request->get('p24_order_id')
		);

		if ($transactionValid) {
			$order = Order::where(['session_id' => $request->get('p24_session_id')])->first();
			$order->paid = true;
			$order->external_id = $request->get('p24_order_id');
			$order->transfer_title = $request->get('p24_statement');
			$order->save();
		}

	}

}
