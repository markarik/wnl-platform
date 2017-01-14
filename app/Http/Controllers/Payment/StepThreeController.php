<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Lib\Przelewy24\Client as Payment;
use Illuminate\Support\Facades\Auth;

class StepThreeController extends Controller
{
    public function index(Payment $payment) {

        $user = Auth::user();

        if (!$user){
            return redirect(url('/payment/step1'));
        }

        $order = $user->orders()->recent();

        $order = [
            'session_id'  => $order->session_id,
            'merchant_id' => config('przelewy24.merchant_id'),
            'amount'      => (int) $order->product->price * 100,
            'currency'    => 'PLN',
            'description' => $order->product->name,
            'client'      => $user->full_name,
            'address'     => $user->address,
            'zip'         => $user->zip,
            'city'        => $user->city,
            'country'     => 'PL',
            'email'       => $user->email,
            'language'    => 'pl',
            'url_return'  => url('/profile/orders'),
            'url_status'  => url('/payment/status'),
        ];

        $order['sign'] = $payment::generateChecksum($order['session_id'], $order['amount']);

        return view('payment.step3', [ 'order' => $order ]);
    }

    public function handle() {}

    public function status(Request $request, Payment $payment){
        //TODO: IP filtering
        Log::debug('request:' . json_encode($request->request->all(), JSON_PRETTY_PRINT));
        Log::debug('headers:' . json_encode($request->headers->all(), JSON_PRETTY_PRINT));

        $transactionValid = $payment->verifyTransaction(
            $request->get('p24_session_id'),
            $request->get('p24_amount'),
            $request->get('p24_order_id')
        );

        if ($transactionValid){
            // update order model
        }

    }

}
