<?php

namespace App\Http\Controllers\Payment;

use App\Models\Order;
use App\Models\Payment as PaymentModel;
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

		Log::debug('Order confirmation');

		$order = $user->orders()->recent();
		$amount = (int)$order->total_with_coupon * 100;
		$checksum = $payment::generateChecksum($order->session_id, $amount);

		$viewData = [
			'order' => $order,
			'user' => $user,
			'checksum' => $checksum,
			'amount'      => $amount,
			'returnUrl'  => $this->getReturnUrl($amount),
			'instalments' => null
		];

		$productInstalments = $order->product->paymentMethods
			->where('slug', 'instalments')
			->first();

		if (!empty($productInstalments)) {
			$instalments = $productInstalments->isAvailable() ? $order->instalments['instalments'] : false;
			$firstInstalmentAmount = (int) ((int) $order->total_with_coupon === 0 ? 0 : $instalments[0]['amount'] * 100);
			$instalmentsChecksum = $payment::generateChecksum($order->session_id, $firstInstalmentAmount);

			$viewData['instalments'] = $instalments;
			$viewData['instalmentsChecksum'] = $instalmentsChecksum;
		}

		return view('payment.confirm-order', $viewData);
	}

	public function handle(Request $request)
	{
		$user = Auth::user();
		Log::debug('Saving payment method and redirecting to dashboard.');
		$order = $user->orders()->recent();
		$order->method = $request->input('method');
		$order->save();

		session()->forget(['coupon', 'product']);

		$amount = (int)$order->total_with_coupon * 100;
		return redirect($this->getReturnUrl($amount));
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

		$paymentLog = PaymentModel::where('session_id', $request->get('p24_session_id'))->first();
		$paymentLog->external_id = $externalId;
		$paymentLog->amount = $paidAmount;
		$paymentLog->save();
		$order = $paymentLog->order;

		if ($transactionValid && $paymentLog->status !== 'success') {
			$paymentLog->update(['status' => 'success']);
			$order->paid_amount += $paidAmount;
			$order->external_id = $externalId;
			$order->transfer_title = $request->get('p24_statement');
			$order->save();
		} else {
			$paymentLog->status = 'error';
			Log::warning("P24 transaction validation failed - order #{$order->id}");
		}
	}

	private function getReturnUrl($amount) {
		return sprintf('%s&%s', url('app/myself/orders?payment'), "amount={$amount}");
	}
}
