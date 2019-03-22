@extends('layouts.checkout', ['disableCart' => true])

@section('content')
	<div class="t-checkout">
		<div class="payment-content t-account">
			<div class="t-account__row -catalinaBlue">@lang('payment.account-name-heading')</div>
			<div class="t-account__row">
				@include('payment.avatar-large-with-defaults', ['user' => $user])
			</div>
			<div class="t-account__row -textPlus2">{{$user->fullName}}</div>
			<div class="t-account__row -textMinus1 -largeSpace">@lang('payment.account-wrong-account-text') <a class="logout-link a-link">@lang('payment.account-wrong-account-register-text')</a></div>

			<div class="t-account__row -divider -largeSpace"></div>
			<div class="t-account__row">
				<span>@lang('payment.account-product-bought-info')</span>
			</div>
			<a class="a-button -big t-account__row" href="{{url('/')}}" data-button="account-continue">
				@lang('payment.account-back-to-course')
			</a>
		</div>
	</div>
@endsection
