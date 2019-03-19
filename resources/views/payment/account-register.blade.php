@extends('payment.layout')

@section('content')
	<div class="t-checkout">

		@include('payment.cart', [
			'productName' => $product->name,
			'productPrice' => $product->price,
			'productAccessEnd' => $product->access_end,
			'productPriceWithCoupon' => $productPriceWithCoupon,
			'coupon' => $coupon,
		])
		<div class="payment-content t-checkout__content">
			@include('payment.stepper', ['currentStep' => 0])
			@if (!$errors->isEmpty())
				<section class="subsection">
					<div class="notification is-warning has-text-centered">@lang('payment.account-errors')</div>
				</section>
			@endif

			{!! form_start($form)  !!}

			<section class="section">
				<p>@lang('payment.account-register-login-text') <a class="opens-login-modal">@lang('payment.account-register-login-button')</a></p>
				<h2 class="title">@lang('payment.account-register-heading')</h2>
				{!! form_row($form->email) !!}
				{!! form_row($form->password) !!}
			</section>

			<section class="form-end">
				<div class="block">
					<button class="a-button -big" data-button="account-continue">
						@lang('payment.account-register-submit')
					</button>
				</div>
			</section>

			<input type="hidden" name="edit" value="{{ request('edit') }}">
			{!! form_end($form, false)  !!}

				@lang('payment.account-tou-content', [
					'tou-link-content' => trans('payment.account-tou-link-content'),
					'tou-link-href' => trans('payment.tou-link-href'),
					'privacy-link-href' => trans('payment.privacy-link-href'),
				])

		</div>
	</div>

@endsection
