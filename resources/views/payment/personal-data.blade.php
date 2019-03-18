@extends('payment.layout')

@section('content')

	@include('payment.cart', [
		'productName' => $product->name,
		'productPrice' => $product->price,
		'productAccessEnd' => $product->access_end,
		'productPriceWithCoupon' => $productPriceWithCoupon,
		'coupon' => $coupon,
	])

	<div class="payment-content t-checkout__content">
		@if (!$errors->isEmpty())
			<section class="subsection">
				<div class="notification is-warning has-text-centered">@lang('payment.personal-data-errors')</div>
			</section>
		@endif

		{!! form_start($form)  !!}

		<section class="section">
			<h2 class="title">@lang('payment.personal-data-account-heading')</h2>
			{!! form_row($form->first_name) !!}
			{!! form_row($form->last_name) !!}
		</section>

		<section class="section">
			<h3 class="title">@lang('payment.personal-data-id-heading')</h3>

			<div class="m-form-group">
				<h4>{!! form_label($form->identity_number) !!}</h4>
				<div class="identity-number-select">
					{!! form_widget($form->identity_number_type) !!}
				</div>
				{!! form_widget($form->identity_number) !!}
				{!! form_errors($form->identity_number) !!}
			</div>
		</section>

		<section id="personal-data" class="section">
			@if(empty($coupon) || $coupon->kind !== \App\Models\Coupon::KIND_PARTICIPANT)
				<h2 class="title">@lang('payment.personal-data-heading')</h2>

				{!! form_row($form->recipient) !!}
				{!! form_row($form->address) !!}
				<div class="-grouped">
					{!! form_row($form->zip) !!}
					{!! form_row($form->city) !!}
				</div>
				{!! form_row($form->phone) !!}
			@endif

			<h2 class="title">@lang('payment.personal-data-invoice-data-heading')</h2>
			<div class="box">
				<div id="personal-data-invoice-toggle">
					{!! form_row($form->invoice) !!}
				</div>
				<div id="personal-data-invoice-form"
					 class="@if (Session::get('_old_input.invoice')) show @else hidden @endif">
					{!! form_row($form->invoice_name) !!}
					{!! form_row($form->invoice_nip) !!}
					{!! form_row($form->invoice_address) !!}
					<div class="-grouped">
						{!! form_row($form->invoice_zip) !!}
						{!! form_row($form->invoice_city) !!}
					</div>
					{!! form_row($form->invoice_country ) !!}
			</div>
		</section>

		<section class="form-end">
			<div class="block has-text-centered">
				<button class="button is-primary">
						@lang('payment.personal-data-submit')
				</button>
			</div>
		</section>

		<input type="hidden" name="edit" value="{{ request('edit') }}">
		{!! form_end($form, false)  !!}

	</div>

@endsection
