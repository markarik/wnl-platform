@extends('payment.layout')

@section('content')
	<div class="container payment-content">
		@include('payment.stepper', ['currentStep' => 0])
		<div class="block has-text-centered">
			<div><strong>@lang('payment.account-name-heading')</strong></div>

			<div>
				@if ($user->profile->avatar_url)
					<img title="{{$user->fullName}}" src="{{$user->profile->avatar_url}}" />
				@else
					<div title="{{$user->fullName}}">{{$user->initials}}</div>
				@endif
			</div>
			<div><strong>{{$user->fullName}}</strong></div>
			<div><small>@lang('payment.account-wrong-account-text') <a class="logout-link">@lang('payment.account-wrong-account-register-text')</a></small></div>


			<a class="a-button -big" href="{{route('payment-personal-data')}}" data-button="account-continue">
				@lang('payment.account-name-submit')
			</a>
		</div>
	</div>

@endsection
