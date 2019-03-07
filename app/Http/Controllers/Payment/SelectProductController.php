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

		$products = Product::all()->keyBy('slug');
		$onsite = $products['wnl-online-onsite'] ?? null;
		$online = $products['wnl-online'] ?? null;

		if (!$onsite || !$online) {
			return view('payment.no-product');
		}

		return view('payment.select-product', compact('online', 'onsite', 'participantCoupon'));
	}
}
