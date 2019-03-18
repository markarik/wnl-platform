<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\UseCoupon;
use App\Models\Product;
use Auth;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;

class VoucherController extends Controller
{
	public function index(Request $request)
	{
		if (Session::has('productId')) {
			$product = Product::find(Session::get('productId'));
		} else {
			$product = Product::slug($request->route('productSlug') ?? Product::SLUG_WNL_ONLINE);
			Session::put('productId', $product->id);
		}

		if (!$product instanceof Product ||
			!$product->available ||
			$product->signups_close->isPast() ||
			$product->signups_start->isFuture()
		) {
			return view('payment.signups-closed', ['product' => $product]);
		}

		$user = Auth::user();
		$coupon = $this->readCoupon($user);

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

	protected function readCoupon($user) {
		$userCoupon = $user ? $user->coupons->first() : null;
		if (session()->has('coupon')) {
			return session()->get('coupon')->fresh();
		} else {
			return $userCoupon;
		}
	}
}
