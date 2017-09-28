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
		$coupon = Coupon::select()
			->where('code', $request->code)
			->where(function ($query) {
				$query
					->where('times_usable', '>', 0)
					->orWhere('times_usable', null);
			})
			->where('expires_at', '>', Carbon::now())
			->first();

		session()->put('coupon', $coupon);

		return redirect()->route('payment-select-product');
	}
}
