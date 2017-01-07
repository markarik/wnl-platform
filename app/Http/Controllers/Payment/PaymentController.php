<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
	public function step3(Przelewy24 $przelewy24) {

		$order = [
			'session_id'  => str_random(),
			'merchant_id' => config('przelewy24.merchant_id'),
			'amount'      => 200000,
			'currency'    => 'PLN',
			'description' => 'Kurs internetowy + warsztaty',
			'client'      => 'Prezes Chrupek',
			'address'     => 'ul. Asnyka 1',
			'zip'         => '66-777',
			'city'        => 'Poznań',
			'country'     => 'PL',
			'email'       => 'jlkarminski@gmail.com',
			'language'    => 'pl',
			'url_return'  => url('/dashboard'),
			'url_status'  => url('/payment/status'),
		];

		$order['sign'] = $przelewy24::generateChecksum($order['session_id'], $order['amount']);

		return view('payment.step3', [ 'order' => $order ]);
	}

	public function status(Request $request, Przelewy24 $przelewy24) {
		//TODO: IP filtering
		\Log::debug('request:' . json_encode($request->request->all(), JSON_PRETTY_PRINT));
		\Log::debug('headers:' . json_encode($request->headers->all(), JSON_PRETTY_PRINT));

		$transactionValid = $przelewy24->verifyTransaction(
			$request->get('p24_session_id'),
			$request->get('p24_amount'),
			$request->get('p24_order_id')
		);

		if ($transactionValid){
			// update order model
		}

	}
}
