@extends('layouts.checkout')

@section('content')
	<div class="t-checkout">
		@include('payment.cart')
		<div class="t-checkout__content">
			<div class="o-voucher o-column">
				<img src="{{ asset('images/voucher_page_hero.svg') }}" class="o-column__row -largeSpace"/>
				<header class="o-column__row">
					<h2 class="o-voucher__headline">Mask kod? Super 🎉</h2>
					<h2 class="o-voucher__headline">Wpisz go poniżej, aby wykorzystać zniżkę.</h2>
				</header>
				<form action="{{ route('payment-voucher-post') }}" method="post" class="m-formGroup o-column">
					{!! csrf_field() !!}
					<div class="o-column__row -largeSpace">
						<label for="code" class="a-label">@lang('payment.voucher-label')</label>
						<input
							type="text"
							id="code"
							name="code"
							value="{{ request('code') ?? session('_old_input.code') ?? '' }}"
							class="a-input"
						>
						@foreach ($errors->get('code') as $message)
							<div class="a-error">{{ $message }}</div>
						@endforeach
					</div>
					<div class="o-column__row -textCenter">
						<button type="submit" class="a-button -big">
							@lang('payment.voucher-submit')
						</button>
					</div>
					<a href="{{ route('payment-account') }}" class="a-linkInText -stormGray -textMinus1 -textCenter">
						@lang('payment.voucher-skip')
					</a>
				</form>
			</div>
		</div>
	</div>
@endsection
