<?php

namespace App\Http\Controllers\Payment;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SelectProductController extends Controller
{
	public function index()
	{

		$products = Product::select(['quantity', 'slug'])->get()->keyBy('slug');
		$onsite = $products['wnl-online-onsite'];
		$online = $products['wnl-online'];

		return view('payment.select-product', [
			'onsite' => $onsite,
			'online' => $online,
		]);
	}
}
