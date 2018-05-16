<div>
	<section class="hero is-primary is-bold">
		<div class="hero-body has-text-centered">
			<div class="container">
				<div id="payment-steps" class="columns is-mobile">
					<div class="column is-one-third">
						@if ($step > 1) <a href="{{ route('payment-select-product') }}"> @endif
							<div class="payment-step @if ($step > 0) is-active @endif @if ($step === 1) is-current @endif">
								<span class="payment-step-count">@lang('payment.payment-steps-select-product-count')</span>
								<span class="payment-step-text is-hidden-mobile">@lang('payment.payment-steps-select-product')</span>
							</div>
						@if ($step > 1) </a> @endif
					</div>

					<div class="column is-one-third">
						@if ($step > 2) <a href="{{ route('payment-personal-data') }}"> @endif
							<div class="payment-step @if ($step > 1) is-active @endif @if ($step === 2) is-current @endif">
								<span class="payment-step-count">@lang('payment.payment-steps-personal-data-count')</span>
								<span class="payment-step-text is-hidden-mobile">@lang('payment.payment-steps-personal-data')</span>
							</div>
						@if ($step > 2) </a> @endif
					</div>

					<div class="column is-one-third">
						<div class="payment-step @if ($step > 2) is-active @endif @if ($step === 3) is-current @endif">
							<span class="payment-step-count">@lang('payment.payment-steps-confirm-order-count')</span>
							<span class="payment-step-text is-hidden-mobile">@lang('payment.payment-steps-confirm-order')</span>
						</div>
					</div>
				</div>
				<h1 class="title">
					{{ $title }}
				</h1>
				<p class="subtitle">
					{!! $subtitle  !!}
				</p>
			</div>
		</div>
	</section>
	@if(
		(isset($online) && $online->signups_end->isPast() && $online->signups_close->isFuture())
		|| (Session::has('product') && Session::get('product')->signups_end->isPast() && Session::get('product')->signups_close->isFuture())
	)
		<section class="notification is-danger has-text-centered">
			Pamiętaj! W tym momencie otwarta jest dodatkowa pula zapisów, dla której nie możemy już niestety zagwarantować terminowego dostarczenia Albumu Map Myśli. :(<br>
			Zrobimy jednak co w naszej mocy, żeby trafił do Ciebie jak najszybciej! :)
		</section>
	@endif

	@if (Session::has('coupon'))
		<section class="voucher notification is-info has-text-centered">
			@lang('payment.voucher-current', [
				'name' => session('coupon')['name'],
				'value' => trans('payment.voucher-' . session('coupon')['type'], [
					'value' => session('coupon')['value'],
				])
			])
		</section>
	@elseif (Auth::user() && Auth::user()->coupons->count() !== 0)
		<section class="voucher notification is-info has-text-centered">
			@lang('payment.voucher-current', [
				'name' => Auth::user()->coupons[0]['name'],
				'value' => trans('payment.voucher-' . Auth::user()->coupons[0]['type'], [
					'value' => Auth::user()->coupons[0]['value'],
				])
			])
			<p>@lang('payment.voucher-current-explanation')</p>
		</section>
	@endif
</div>
