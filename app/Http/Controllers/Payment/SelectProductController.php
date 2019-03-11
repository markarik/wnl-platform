<?php

namespace App\Http\Controllers\Payment;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SelectProductController extends Controller
{
	public function index()
	{
		$user = \Auth::user();
		$participantCoupon = false;
		if ($user) {
			$participantCoupon = \Auth::user()->coupons()->where('slug', 'wnl-online-only')->count() > 0;
		}

		$products = Product::whereIn('slug', ['wnl-online-onsite', 'wnl-online'])->get()->keyBy('slug');

		if ($products->count() === 0) {
			return view('payment.no-product');
		}

		if ($products->count() === 1) {
			return redirect()->route('payment-personal-data', ['product' => $products->first()->slug]);
		}

		return view('payment.select-product', [
			'online'            => $products['wnl-online'],
			'onsite'            => $products['wnl-online-onsite'],
			'participantCoupon' => $participantCoupon,
		]);
	}
}
