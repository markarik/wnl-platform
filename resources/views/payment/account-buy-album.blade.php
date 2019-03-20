@extends('layouts.checkout', ['disableCart' => true])

@section('content')
	<div class="t-checkout">
		<div class="payment-content t-account">
			<div class="t-account__row -catalinaBlue">@lang('payment.account-name-heading')</div>
			<div class="t-account__row">
				@if (!empty($user->profile->avatar_url))
					<img src="{{$user->profile->avatar_url}}" class="a-avatar -large"/>
				@elseif (!empty($user->initials))
					<span class="a-avatar -automatic -large">
					{{$user->initials}}
				</span>
				@else
					<i class="o-navigation__item a-icon a-avatar -large fa-user"></i>
				@endif
			</div>
			<div class="t-account__row -textPlus2">{{$user->fullName}}</div>
			<div class="t-account__row -textMinus1 -largeSpace">@lang('payment.account-wrong-account-text') <a class="logout-link">@lang('payment.account-wrong-account-register-text')</a></div>

			<div class="t-account__row -divider -largeSpace"></div>
			<div class="t-account__row">
				<span>@lang('payment.account-product-bought-info')</span>
				<span class="-textMedium">@lang('payment.account-buy-album-confirmation')</span>
			</div>
			<a class="a-button -big t-account__row" href="{{route('payment-personal-data', ['slug' => \App\Models\Product::SLUG_WNL_ALBUM])}}" data-button="account-continue">
				@lang('payment.account-buy-album')
			</a>
		</div>
	</div>
@endsection
