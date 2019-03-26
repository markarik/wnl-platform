<?php

namespace App\Traits;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait CheckoutTrait
{
	/**
	 * @param Request $request
	 * @return Product|null
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 */
	private function getProduct(Request $request): ?Product
	{
		$product = null;
		$productSlugParam = $request->route('productSlug');

		if ($productSlugParam) {
			$product = Product::slug($productSlugParam);
		} else if (Session::has('productId')) {
			$product = Product::find(Session::get('productId'));
		}

		if (!$product || !$product->exists()) {
			$product = Product::slug(Product::SLUG_WNL_ONLINE);
		}

		Session::put('productId', $product->id);

		return $product;
	}

	private function readCoupon(Product $product, ?User $user): ?Coupon {
		/** @var Coupon $coupon */
		$coupon = $user ? $user->coupons->last() : null;

		if (session()->has('coupon')) {
			$sessionCoupon = session()->get('coupon');

			if ($sessionCoupon instanceof Coupon) {
				$coupon = $sessionCoupon->fresh();
			}
		}

		if ($coupon instanceof Coupon && !$coupon->isApplicableForProduct($product)) {
			$coupon = null;
		}

		return $coupon;
	}

	private function canBuyAlbum(Request $request) {
		$user = Auth::user();

		$hasProlongedCourse = $user->orders->filter(function($order) {
				return $order->paid && !$order->canceled && $order->coupon && $order->coupon->kind === Coupon::KIND_PARTICIPANT;
			})->count() > 0;
		$hasBoughtAlbum = $user->getProducts()->filter(function($product) {
				return $product->slug === Product::SLUG_WNL_ALBUM;
			})->count() > 0;

		return !$hasBoughtAlbum && $hasProlongedCourse;
	}
}
