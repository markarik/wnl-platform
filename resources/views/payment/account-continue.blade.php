@extends('payment.layout')

@section('content')
	<div class="t-checkout">
		@include('payment.cart', [
			'productName' => $product->name,
			'productPrice' => $product->price,
			'productAccessEnd' => $product->access_end,
			'productPriceWithCoupon' => $productPriceWithCoupon,
			'coupon' => $coupon,
		])
		<div class="payment-content t-checkout__content t-account">
		<span class="-mischka t-account__row -x-large a-icon -largeSpace">
			<i class="fa fa-shopping-cart"></i>
		</span>
			<h2 class="t-account__row -textPlus2 -largeSpace">@lang('payment.account-continue-heading')</h2>
			<div class="m-buttonWithNote t-account__row">
				<a class="a-button -big" href="{{route('payment-personal-data')}}" data-button="account-continue">
					@lang('payment.account-continue-submit')
				</a>
				<a href="https://wiecejnizlek.pl" class="m-buttonWithNote__note -stormGray">@lang('payment.account-continue-back-link')</a>
			</div>
		</div>
	</div>

@endsection
