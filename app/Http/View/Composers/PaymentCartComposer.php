<?php

namespace App\Http\View\Composers;

use App\Models\Coupon;
use App\Traits\CheckoutTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentCartComposer
{
	use CheckoutTrait;

	protected $product;
	protected $coupon;

	public function __construct(Request $request)
	{
		$this->product = $this->getProduct($request);
		$this->coupon = $this->readCoupon($this->product, Auth::user());
	}

	/**
	 * Bind data to the view.
	 *
	 * @param  View  $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$coupon = $view->coupon ?? $this->coupon;

		$view->with('productName', $view->productName ?? $this->product->name);
		$view->with('productPrice', $view->productPrice ?? $this->product->price);
		$view->with('productAccessEnd', $view->productAccessEnd ?? $this->product->access_end);
		$view->with('productPriceWithCoupon', $view->productPriceWithCoupon ?? $this->product->getPriceWithCoupon($this->coupon));
		$view->with('coupon', $coupon);
		$view->with('hasParticipantCoupon', $coupon ?  $coupon->kind === Coupon::KIND_PARTICIPANT : false);
	}
}
