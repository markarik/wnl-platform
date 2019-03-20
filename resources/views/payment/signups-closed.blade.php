@extends('layouts.checkout', ['disableCart' => true])

@section('content')

<section class="section">
	<div class="container">
		@if(!$product || $product->signups_close->isPast())
			<div class="column">
				<div class="notification has-text-centered strong">@lang('payment.signups-closed-past')</div>
			</div>
		@endif
		@if($product->signups_start->isFuture())
			<div class="notification has-text-centered strong">
				@lang('payment.signups-closed-countdown')
				<div class="signups-countdown" data-start="{{ $product->signups_start->timestamp }}">
					@lang('payment.signups-closed-countdown-loading')
				</div>
			</div>
			<div class="notification has-text-centered strong">
				@lang('payment.signups-closed-leave-email')
			</div>
		@endif
		@if(!$product->available)
			<div class="column">
				<div class="notification has-text-centered strong">
					<p>@lang('payment.signups-closed-not-available')</p>
					<p>@lang('payment.signups-closed-leave-email')</p>
				</div>
			</div>
		@endif
	</div>
</section>
@endsection
