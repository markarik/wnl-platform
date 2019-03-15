<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\UseCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Validator;

class VoucherController extends Controller
{
	public function index(Request $request)
	{
		return view('payment.voucher');
	}

	public function handle(UseCoupon $request)
	{
		$code = mb_convert_case($request->code, MB_CASE_UPPER, "UTF-8");
		$coupon = Coupon::validCode($code);
		session()->put('coupon', $coupon);

		return redirect()->route('payment-account');
	}
}
