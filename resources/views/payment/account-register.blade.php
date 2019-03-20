@extends('layouts.checkout')

@section('content')
	<div class="t-checkout">

		@include('payment.cart', [
			'productName' => $product->name,
			'productPrice' => $product->price,
			'productAccessEnd' => $product->access_end,
			'productPriceWithCoupon' => $productPriceWithCoupon,
			'coupon' => $coupon,
		])
		<div class="t-checkout__content">
			@include('payment.stepper', ['currentStep' => 0])

			@if (!$errors->isEmpty())
				<section class="subsection">
					<div class="notification is-warning has-text-centered">@lang('payment.account-errors')</div>
				</section>
			@endif

			{!! form_start($form)  !!}

			<p class="t-checkout__content__row">
				@lang('payment.account-register-login-text')
				<a class="opens-login-modal a-link">@lang('payment.account-register-login-button')</a>
			</p>
			<h2 class="-textPlus3 t-checkout__content__row">@lang('payment.account-register-heading')</h2>
			<div class="t-checkout__content__row">
				{!! form_row($form->email) !!}
				<div class="m-formGroup">
					{!! form_label($form->password) !!}
					<div class="m-formGroup__inputWrapper">
						{!! form_widget($form->password) !!}
						<i class="a-icon fa-eye -touchable" id="passwordVisibilityToggle"></i>
					</div>
					{!! form_errors($form->password) !!}
				</div>
			</div>

			<div class="m-buttonWithNote t-checkout__content__row">
				<button class="a-button -big" data-button="account-continue">
					@lang('payment.account-register-submit')
				</button>
				<p class="m-buttonWithNote__note">
					@lang('payment.account-tou-content', [
						'tou-link-content' => trans('payment.account-tou-link-content'),
						'tou-link-href' => trans('payment.tou-link-href'),
						'privacy-policy-link-href' => trans('payment.privacy-policy-link-href')
					])
				</p>
			</div>

			<input type="hidden" name="edit" value="{{ request('edit') }}">
			{!! form_end($form, false)  !!}
		</div>
	</div>

@endsection
