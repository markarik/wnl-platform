@extends('layouts.checkout')

@section('content')
	<div class="t-checkout p-account">
		@include('payment.cart')
		<div class="t-checkout__content t-account o-column">
			<i class="-mischka o-column__row -x-large a-icon -largeSpace fa-shopping-cart"></i>
			<h2 class="o-column__row -textPlus2 -largeSpace">@lang('payment.account-continue-heading')</h2>
			<div class="m-buttonWithNote o-column__row -stormGray">
				<a
					class="a-button -big"
					href="{{route('payment-personal-data', ['slug' => \App\Models\Product::SLUG_WNL_ONLINE])}}"
					data-button="account-continue"
				>
					@lang('payment.account-continue-submit')
				</a>
				<a href="https://wiecejnizlek.pl" class="m-buttonWithNote__note a-linkInText">@lang('payment.account-continue-back-link')</a>
			</div>
		</div>
	</div>
@endsection
