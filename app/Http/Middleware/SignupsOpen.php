<?php

namespace App\Http\Middleware;

use App\Models\Product;
use App\Traits\CheckoutTrait;
use Closure;

class SignupsOpen
{
	use CheckoutTrait;
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$product = $this->getProduct($request);
		if ($this->isSignupForProductClosed($product)) {
				return response(view('payment.signups-closed', ['product' => $product]));
		}

		return $next($request);
	}

	private function isSignupForProductClosed(Product $product): bool
	{
		return !$product->available ||
			$product->signups_close->isPast() ||
			$product->signups_start->isFuture();
	}

}
