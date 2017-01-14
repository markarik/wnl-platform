<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Order;
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

        $order = Order::where(['session_id' => $sessionId])->update(['method' => $method]);

        if ($order){
            return response()->json(['status' => 'success']);
        }
    }
}
