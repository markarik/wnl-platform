<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

trait CheckoutTrait
{
	private function getProduct(Request $request): Product
	{
		$productSlugParam = $request->route('productSlug');

		if ($productSlugParam) {
			$product = Product::slug($productSlugParam);
			Session::put('productId', $product->id);
		} else if (Session::has('productId')) {
			$product = Product::find(Session::get('productId'));
		} else {
			$product = Product::slug(Product::SLUG_WNL_ONLINE);
			Session::put('productId', $product->id);
		}

		return $product;
	}
}
