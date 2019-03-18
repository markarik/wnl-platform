<?php

namespace App\Http\Controllers\Payment;

use App\Models\Order;
use App\Models\OrderInstalment;
use App\Models\Payment as PaymentModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Lib\Przelewy24\Client as Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ConfirmOrderController extends Controller
{
	public function index(Payment $payment)
	{
		$user = Auth::user();
		Log::debug('Order confirmation');

		/** @var Order $order */
		$order = $user->orders()->recent();

		$coupon = $order->coupon;
		$productPriceWithCoupon = $order->total_with_coupon;
		$couponValue = null;

		$amount = (int)$productPriceWithCoupon * 100;
		$checksum = $payment::generateChecksum($order->session_id, $amount);

		$viewData = [
			'order' => $order,
			'user' => $user,
			'checksum' => $checksum,
			'amount'      => $amount,
			'returnUrl'  => $this->getReturnUrl($amount),
			'instalments' => null,
			'coupon' => $coupon,
			'productPriceWithCoupon' => $productPriceWithCoupon
		];

		$paymentMethodInstalments = $order->product->paymentMethods
			->where('slug', 'instalments')
			->first();

		if (!empty($paymentMethodInstalments) && $paymentMethodInstalments->isAvailable()) {
			$paymentSchedule = $order->generatePaymentSchedule();

			/** @var OrderInstalment $firstInstalment */
			$firstInstalment = $paymentSchedule->first();
			$firstInstalmentAmount = (int) ((int) $order->total_with_coupon === 0 ? 0 : $firstInstalment->amount * 100);
			$instalmentsChecksum = $payment::generateChecksum($order->session_id, $firstInstalmentAmount);

			$viewData['instalments'] = $paymentSchedule;
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

		Session::forget(['coupon', 'productId', 'orderId']);

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
			Log::warning("P24 transaction validation failed - order #{$order->id}");
		}
	}

	private function getReturnUrl($amount) {
		return sprintf('%s&%s', url('app/myself/orders?payment'), "amount={$amount}");
	}
}
