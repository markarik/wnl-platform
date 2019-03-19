@extends('payment.layout')

@section('content')
	@include('payment.cart', [
		'productName' => $product->name,
		'productPrice' => $product->price,
		'productAccessEnd' => $product->access_end,
		'productPriceWithCoupon' => $productPriceWithCoupon,
		'coupon' => $coupon,
	])
	<div class="payment-content t-checkout__content">
		@include('payment.stepper', ['currentStep' => 0])
		<div class="block has-text-centered">
			<div><strong>@lang('payment.account-continue-heading')</strong></div>

			<div>
				<a class="a-button -big" href="{{route('payment-personal-data')}}" data-button="account-continue">
					@lang('payment.account-continue-submit')
				</a>
			</div>
			<a href="https://wiecejnizlek.pl">@lang('payment.account-continue-back-link')</a>
		</div>
	</div>

@endsection
