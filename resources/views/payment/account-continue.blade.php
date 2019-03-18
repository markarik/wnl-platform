@extends('payment.layout')

@section('content')
	<div class="container payment-content">
		<div class="block has-text-centered">
			<div><strong>@lang('payment.account-continue-heading')</strong></div>

			<div>
				<a class="button is-primary" href="{{route('payment-personal-data')}}">
					@lang('payment.account-continue-submit')
				</a>
			</div>
			<a href="https://wiecejnizlek.pl">@lang('payment.account-continue-back-link')</a>
		</div>
	</div>

@endsection
