<?php

namespace App\Traits;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

trait CheckoutTrait
{
	private function getProduct(Request $request): ?Product
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

	private function isSignupForProductClosed(?Product $product): bool
	{
		return !$product instanceof Product ||
			!$product->available ||
			$product->signups_close->isPast() ||
			$product->signups_start->isFuture();
	}

	private function readCoupon(Product $product, ?User $user): ?Coupon {
		/** @var Coupon $coupon */
		$coupon = $user ? $user->coupons->first() : null;

		if (session()->has('coupon')) {
			$sessionCoupon = session()->get('coupon');

			if (!$sessionCoupon instanceof Coupon) {
				$coupon = null;
			} else {
				$coupon = session()->get('coupon')->fresh();
			}
		}

		if ($coupon instanceof Coupon && !$coupon->isApplicableForProduct($product)) {
			$coupon = null;
		}

		return $coupon;
	}
}
