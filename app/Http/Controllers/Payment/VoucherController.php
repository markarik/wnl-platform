<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
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

	public function handle(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'code' => 'required|alpha_num',
		]);

		$code = strtoupper($request->code);

		if ($request->has('code')) {
			$coupon = $this->validateVoucher($code);
			$validator->after(function ($validator) use ($coupon) {
				if (!$coupon) {
					$validator->errors()->add(
						'code',
						trans('payment.voucher-is-invalid')
					);
				}
			});
		}

		if ($validator->fails()) {
			return redirect()
				->route('payment-voucher')
				->withErrors($validator)
				->withInput();
		}

		session()->put('coupon', $coupon);

		return redirect()->route('payment-select-product');
	}

	protected function validateVoucher($code)
	{
		$coupon = Coupon::select()
			->where('code', $code)
			->where(function ($query) {
				$query
					->where('times_usable', '>', 0)
					->orWhere('times_usable', null);
			})
			->where('expires_at', '>', Carbon::now())
			->first();

		if (!$coupon) return false;

		return $coupon;
	}
}
