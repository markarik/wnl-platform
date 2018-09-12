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
			Log::notice('Auth failed, redirecting...');

			return redirect(route('payment-select-product'));
		}

		$order = $user->orders()->recent();

		$checksum = $payment::generateChecksum($order->session_id, (int)$order->total_with_coupon * 100);
		Log::debug('Order confirmation');

		$instalments = $order->product->paymentMethods
			->where('slug', 'instalments')
			->first()
			->isAvailable() ? $order->instalments['instalments'] : false;

		return view('payment.confirm-order', [
			'order'       => $order,
			'user'        => $user,
			'checksum'    => $checksum,
			'instalments' => $instalments,
		]);
	}

	public function handle(Request $request)
	{
		$user = Auth::user();
		Log::debug('Saving payment method and redirecting to dashboard.');
		$order = $user->orders()->recent();
		$order->method = $request->input('method');
		$order->save();

		session()->forget(['coupon', 'product']);

		return redirect(url('/app/myself/orders?payment'));
	}

	public function status(Request $request, Payment $payment)
	{
		Log::debug('request:' . json_encode($request->request->all(), JSON_PRETTY_PRINT));
		Log::debug('headers:' . json_encode($request->headers->all(), JSON_PRETTY_PRINT));

		$transactionValid = $payment->verifyTransaction(
			$request->get('p24_session_id'),
			$request->get('p24_amount'),
			$request->get('p24_order_id')
		);

		$externalId = $request->get('p24_order_id');
		$paidAmount = $request->get('p24_amount') / 100;

		$paymentLog = \App\Models\Payment::where('session_id', $request->get('p24_session_id'))->first();
		$paymentLog->external_id = $externalId;
		$paymentLog->amount = $paidAmount;
		$order = $paymentLog->order;

		if ($transactionValid) {
			$order->paid = true;
			// TODO this has to be changed to handle partial order payment via instalments
			$order->paid_amount = $paidAmount;
			$order->external_id = $externalId;
			$order->transfer_title = $request->get('p24_statement');
			$order->save();

			$paymentLog->status = 'success';
		} else {
			$paymentLog->status = 'error';
			Log::warning('P24 transaction validation failed');
		}
		$paymentLog->save();
	}

}
