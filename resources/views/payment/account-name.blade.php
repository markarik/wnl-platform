@extends('layouts.checkout', ['disableCart' => true])

@section('content')
	<div class="t-account o-column">
		<div class="o-column__row -catalinaBlue">@lang('payment.account-name-heading')</div>
		<div class="o-column__row">
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
		<div class="o-column__row -textPlus2">{{$user->fullName}}</div>
		<div class="o-column__row -textMinus1 -largeSpace">@lang('payment.account-wrong-account-text')
			<a class="logout-link a-link">@lang('payment.account-wrong-account-register-text')</a>
		</div>


		<a class="a-button -big o-column__row" href="{{route('payment-personal-data')}}" data-button="account-continue">
			@lang('payment.account-name-submit')
		</a>
	</div>
@endsection

@section('payment-scripts')
	<script>typeof fbq === 'function' && fbq('track', 'Lead', {platform: '{{config('app.instance_name')}}'})</script>
@endsection
