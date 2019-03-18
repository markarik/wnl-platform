@extends('payment.layout')

@section('content')

	<section class="section">
		<div class="container">
			@if (Auth::user() && Auth::user()->coupons->count() > 0)
				<div class="notification has-text-centered">
					@lang('payment.voucher-already-has', [ 'name' => Auth::user()->coupons[0]['name'] ])
					<p>
						<a href="{{ route('payment-account') }}">
							@lang('payment.voucher-skip')
						</a>
					</p>
				</div>
			@endif
			<div class="voucher-code">
				<form action="{{ route('payment-voucher-post') }}" method="post">
					{!! csrf_field() !!}

					<p><label for="code">@lang('payment.voucher-label')</label></p>
					<input type="text" class="code-input" id="code" name="code" placeholder="XXXXXXXX" value="{{ request('code') ?? session('_old_input.code') ?? '' }}">
					@foreach ($errors->get('code') as $message)
						<div class="is-error">{{ $message }}</div>
					@endforeach
					<p class="margin vertical has-text-centered">
						<button type="submit" class="button is-primary">
							@lang('payment.voucher-submit')
						</button>
					</p>
					<p class="voucher-skip has-text-centered">
						<a href="{{ route('payment-account') }}">
							@lang('payment.voucher-skip')
						</a>
					</p>
				</form>
			</div>
		</div>
	</section>

@endsection
