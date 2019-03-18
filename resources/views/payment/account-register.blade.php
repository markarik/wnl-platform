@extends('payment.layout')

@section('content')
	@include('payment.payment-hero', [
		'step' => 1,
		'title' => 'Lorem ipsum',
		'subtitle' => 'Bacon ipsum',
	])

	<div class="container payment-content">
		@if (!$errors->isEmpty())
			<section class="subsection">
				<div class="notification is-warning has-text-centered">@lang('payment.account-errors')</div>
			</section>
		@endif

		{!! form_start($form)  !!}

		<section class="section">
			<div><small>@lang('payment.account-register-login-text') <a class="opens-login-modal">@lang('payment.account-register-login-button')</a></small></div>
			<div class="form-header has-text-centered">
				<h2 class="title">@lang('payment.account-register-heading')</h2>
			</div>
			<div class="form-group">
				<div class="control">
					{!! form_label($form->email) !!}
					{!! form_widget($form->email) !!}
					{!! form_errors($form->email) !!}
				</div>

				<div class="control">
					{!! form_label($form->password) !!}
					{!! form_widget($form->password) !!}
					{!! form_errors($form->password) !!}
				</div>
			</div>
		</section>

		<section class="form-end">
			<div class="block has-text-centered">
				<button class="button is-primary" data-button="account-continue">
					@lang('payment.account-register-submit')
				</button>
			</div>
		</section>

		<input type="hidden" name="edit" value="{{ request('edit') }}">
		{!! form_end($form, false)  !!}

			@lang('payment.account-tou-content', [
				'tou-link-content' => trans('payment.account-tou-link-content'),
				'tou-link-href' => trans('payment.tou-link-href'),
				'privacy-link-content' => trans('payment.account-privacy-link-content'),
				'privacy-link-href' => trans('payment.privacy-link-href'),
			])

	</div>

@endsection
