<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\UseCoupon;
use App\Traits\CheckoutTrait;
use Auth;
use Illuminate\Http\Request;
use App\Models\Coupon;

class VoucherController extends Controller
{
	use CheckoutTrait;

	public function index(Request $request)
	{
		$product = $this->getProduct($request);

		if ($this->isSignupForProductClosed($product)) {
			return view('payment.signups-closed', ['product' => $product]);
		}

		$user = Auth::user();
		$coupon = $this->readCoupon($product, $user);

		$productPriceWithCoupon = null;

		return view('payment.voucher', [
			'product' => $product,
			'productPriceWithCoupon' => $product->getPriceWithCoupon($coupon),
			'coupon' => $coupon
		]);
	}

	public function handle(UseCoupon $request)
	{
		$code = mb_convert_case($request->code, MB_CASE_UPPER, "UTF-8");
		$coupon = Coupon::validCode($code);
		session()->put('coupon', $coupon);

		return redirect()->route('payment-account');
	}
}
