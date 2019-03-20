<?php

namespace App\Traits;

use App\Exceptions\SignupForProductIsClosedException;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

trait CheckoutTrait
{
	/**
	 * @param Request $request
	 * @return Product|null
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 * @throws SignupForProductIsClosedException
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

		if (!$product) {
			$product = Product::slug(Product::SLUG_WNL_ONLINE);
		}

		Session::put('productId', $product->id);

		if ($this->isSignupForProductClosed($product)) {
			throw new SignupForProductIsClosedException(
				response(view('payment.signups-closed', ['product' => $product]))
			);
		}

		return $product;
	}

	private function isSignupForProductClosed(Product $product): bool
	{
		return !$product->available ||
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
