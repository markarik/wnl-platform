@extends('layouts.checkout')

@section('content')
	<div class="t-checkout">

		@include('payment.cart')
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
				<a id="login-modal-opener" class="a-link">@lang('payment.account-register-login-button')</a>
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

			<div id="login-modal" class="modal">
				<div class="modal-background"></div>
				<div class="modal-card">
					<header class="modal-card-head">
						<p class="modal-card-title"></p>
						<button class="delete" id="login-modal-closer"></button>
					</header>
					<section class="modal-card-body content">
						@include('auth.login-modal')
					</section>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('payment-scripts')
	<script>typeof fbq === 'function' && fbq('track', 'Lead', {platform: '{{config('app.instance_name')}}'})</script>
@endsection
