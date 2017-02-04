@extends('layouts.payment')

@section('content')

	<div class="row">
		<div class="col-xs-12 text-center">
			<h2>@lang('payment.personal-data-title')</h2>
			<div class="alert alert-success">
				@lang('payment.personal-data-product', [ 'name' => $product->name, 'price' => $product->price ])
			</div>
		</div>
	</div>

	{!! form_start($form)  !!}

	<div class="row">
		<div class="col-xs-12 text-center">
			<h2>@lang('payment.personal-data-account-heading')</h2>
			<p class="lead">@lang('payment.personal-data-account-lead')</p>
		</div>

		<div class="col-xs-12">
			<div class="form-group">
				{!! form_row($form->email) !!}

				{!! form_row($form->password) !!}

				{!! form_row($form->password_confirmation) !!}
			</div>
		</div>
	</div>

	<hr>


	<div id="personal-data" class="row">
		<div class="col-xs-12 text-center">
			<h2>@lang('payment.personal-data-heading')</h2>
			<p class="lead">@lang('payment.personal-data-lead')</p>
		</div>

		<div class="col-xs-12">
			<div class="form-group">
				{!! form_row($form->phone) !!}
			</div>
		</div>

		<div class="form-group">
			<div class="col-xs-12 col-sm-6">
				{!! form_row($form->first_name) !!}
			</div>
			<div class="col-xs-12 col-sm-6">
				{!! form_row($form->last_name) !!}
			</div>
			<div class="col-xs-12">
				{!! form_row($form->address) !!}
			</div>
			<div class="col-xs-12 col-sm-3">
				{!! form_row($form->zip) !!}
			</div>
			<div class="col-xs-12 col-sm-9">
				{!! form_row($form->city) !!}
			</div>
		</div>

		<div class="col-xs-12">
			<ul class="list-group">
				<li class="list-group-item">
					<div id="personal-data-invoice-toggle" class="checkbox text-small">
						{!! form_widget($form->invoice) !!}
						{!! form_label($form->invoice) !!}
					</div>
				</li>
			</ul>
		</div>

		<div id="personal-data-invoice-form" class="form-group @if ($form->invoice->getOption('checked')) show @else hidden @endif">
			<div class="col-xs-12">
				{!! form_row($form->invoice_name) !!}
			</div>
			<div class="col-xs-12">
				{!! form_row($form->invoice_nip) !!}
			</div>
			<div class="col-xs-12">
				{!! form_row($form->invoice_address) !!}
			</div>
			<div class="col-xs-12 col-sm-3">
				{!! form_row($form->invoice_zip) !!}
			</div>
			<div class="col-xs-12 col-sm-9">
				{!! form_row($form->invoice_city) !!}
			</div>
			<div class="col-xs-12">
				{!! form_row($form->invoice_country) !!}
			</div>
		</div>
	</div>

	<hr>

	<div class="row">
		<div class="col-xs-12 text-center">
			<h2>@lang('payment.personal-data-consents-heading')</h2>
			<p class="lead">@lang('payment.personal-data-consents-lead')</p>
		</div>


		<div class="col-xs-12">
			<div class="form-group small">
				<ul class="list-group">
					<li class="list-group-item">
						<div class="checkbox">
							{!! form_widget($form->consent_account) !!}
							{!! html_entity_decode(form_label($form->consent_account)) !!}
						</div>
					</li>
					<li class="list-group-item">
						<div class="checkbox">
							{!! form_widget($form->consent_order) !!}
							{!! html_entity_decode(form_label($form->consent_order)) !!}
						</div>
					</li>
				</ul>
			</div>
			<p>@lang('payment.personal-data-consent-newsletter-heading')</p>
			<div class="form-group small">
				<ul class="list-group">
					<li class="list-group-item">
						<div class="checkbox">
							{!! form_widget($form->consent_newsletter) !!}
							{!! html_entity_decode(form_label($form->consent_newsletter)) !!}
						</div>
					</li>
				</ul>
			</div>
			<p>@lang('payment.terms-of-use-link-content')</p>
			<div class="form-group small">
				<ul class="list-group">
					<li class="list-group-item">
						<div class="checkbox">
							{!! form_widget($form->consent_terms) !!}
							{!! html_entity_decode(form_label($form->consent_terms)) !!}
						</div>
					</li>
				</ul>
			</div>
			
		</div>
	</div>

	<div class="text-center">
		<button class="btn btn-primary btn-lg">@lang('payment.personal-data-submit')</button>
	</div>

	{!! form_end($form, false)  !!}

@endsection