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
		@include('payment.stepper', ['currentStep' => 1])
		@if (!$errors->isEmpty())
			<section>
				<div class="notification is-warning has-text-centered">@lang('payment.personal-data-errors')</div>
			</section>
		@endif

		{!! form_start($form)  !!}

		<section class="o-checkoutSection">
			<h2 class="o-checkoutSection__header">@lang('payment.personal-data-account-heading')</h2>
			{!! form_row($form->first_name) !!}
			{!! form_row($form->last_name) !!}
		</section>

		<section class="o-checkoutSection">
			<h3 class="o-checkoutSection__subheader">@lang('payment.personal-data-id-heading')</h3>

			<p class="o-checkoutSection__info">
				@lang('payment.personal-data-id-info')
				<i class="a-icon -cadetBlue fa-info-circle -touchable" id="personal-identity-number-modal-opener"></i>
			</p>
			<div id="personal-identity-number-modal" class="modal">
				<div class="modal-background"></div>
				<div class="modal-card">
					<section class="modal-card-body">
						@include('payment.personal-identity-number-modal')
					</section>
				</div>
			</div>

			{!! form_row($form->personal_identity_number, [
				'wrapper' => [
					'id' => 'personalIdentityNumberGroup',
					'class' => $form->no_identity_number->getValue() ? '-dNone m-formGroup' : 'm-formGroup',
				],
				'label_attr' => ['class' => 'a-label -required'],
			]) !!}
			{!! form_row($form->passport_number, [
				'wrapper' => [
					'id' => 'passportNumberGroup',
					'class' => $form->no_identity_number->getValue() ? 'm-formGroup' : '-dNone  m-formGroup',
				],
				'label_attr' => ['class' => 'a-label -required'],
			]) !!}
			{!! form_row($form->no_identity_number) !!}
		</section>

		<section class="o-checkoutSection">
			@if(empty($coupon) || $coupon->kind !== \App\Models\Coupon::KIND_PARTICIPANT)
				<h2 class="o-checkoutSection__header">@lang('payment.personal-data-heading')</h2>

				{!! form_row($form->recipient) !!}
				{!! form_row($form->address) !!}
				<div class="o-formGroups">
					{!! form_row($form->zip) !!}
					{!! form_row($form->city) !!}
				</div>
				{!! form_row($form->phone) !!}
			@endif
		</section>
		<section class="o-checkoutSection">
			<h2 class="o-checkoutSection__header">@lang('payment.personal-data-invoice-data-heading')</h2>
			<p class="o-checkoutSection__secondaryInfo">@lang('payment.invoice-info')</p>
			{!! form_row($form->invoice) !!}
			<div
				id="personal-data-invoice-form"
				class="@if (Session::get('_old_input.invoice')) show @else hidden @endif"
			>
				{!! form_row($form->invoice_name) !!}
				{!! form_row($form->invoice_nip) !!}
				{!! form_row($form->invoice_address) !!}
				<div class="o-formGroups">
					{!! form_row($form->invoice_zip) !!}
					{!! form_row($form->invoice_city) !!}
				</div>
				{!! form_row($form->invoice_country ) !!}
			</div>
		</section>

		<button class="a-button -big">
				@lang('payment.personal-data-submit')
		</button>

		<input type="hidden" name="edit" value="{{ request('edit') }}">
		{!! form_end($form, false)  !!}

	</div>
	</div>
@endsection
