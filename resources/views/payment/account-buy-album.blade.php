@extends('layouts.checkout', ['disableCart' => true])

@section('content')
	<div class="t-checkout">
		<div class="t-account o-column">
			<div class="o-column__row -catalinaBlue">@lang('payment.account-name-heading')</div>
			<div class="o-column__row">
				@include('payment.avatar-large-with-defaults', ['user' => $user])
			</div>
			<div class="o-column__row -textPlus2">{{$user->fullName}}</div>
			<div class="o-column__row -textMinus1 -largeSpace">@lang('payment.account-wrong-account-text')
				<a class="logout-link a-link">@lang('payment.account-wrong-account-register-text')</a>
			</div>
			<div class="o-column__row -divider -largeSpace"></div>
			<div class="o-column__row">
				<span>@lang('payment.account-product-bought-info')</span>
				<span class="-textMedium">@lang('payment.account-buy-album-confirmation')</span>
			</div>
			<a class="a-button -big o-column__row" href="{{route('payment-personal-data', ['slug' => \App\Models\Product::SLUG_WNL_ALBUM])}}" data-button="account-continue">
				@lang('payment.account-buy-album')
			</a>
		</div>
	</div>
@endsection
