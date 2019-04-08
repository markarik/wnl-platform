<?php


namespace App\Http\Controllers\Api\PublicApi;


use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductsApiController extends Controller
{
	/**
	 * Get products availability
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getAvailability()
	{
		$products = Product::select('slug', 'quantity')->get()->keyBy('slug')->toArray();

		return response()->json($products);
	}
}