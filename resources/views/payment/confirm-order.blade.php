@extends('layouts.payment')

@section('content')
	@include('payment.payment-hero', [
		'step' => 3,
		'title' => trans('payment.confirm-order-title'),
		'subtitle' => trans('payment.confirm-order-subtitle'),
	])

	<div class="container">
		<section class="subsection">
			<div class="box">
				<p class="title">@lang('payment.confirm-order-heading')</p>
				<p><strong>{{ $order->product->name }}</strong></p>
				<p>@lang('payment.confirm-order-price', [ 'price' => $order->product->price ])</p>
			</div>
		</section>

		<section class="subsection">
			<div class="box">
				<p class="title">@lang('payment.confirm-personal-data-heading')</p>
				<p><strong>{{ $user->email }}</strong></p>
				<ul>
					<li>{{ $user->full_name }}</li>
					<li>{{ $user->address }}</li>
					<li>{{ $user->zip }}, {{ $user->city }}</li>
				</ul>
			</div>
		</section>

		@if($user->invoice)
			<section class="subsection">
				<div class="box">
					<p class="title">@lang('payment.personal-data-invoice-heading')</p>
					<ul>
						<li><strong>{{ $user->invoice_name }}</strong></li>
						<li>{{ $user->invoice_address }}</li>
						<li>{{ $user->invoice_zip }}, {{ $user->invoice_city }}</li>
						<li>{{ $user->invoice_country }}</li>
					</ul>
				</div>
			</section>
		@endif

		<section class="subsection">
			<p class="has-text-centered">
				<a href="{{ route('payment-personal-data') }}">@lang('payment.confirm-change-order')</a>
			</p>
		</section>

		<section class="subsection has-text-centered">
			<h2 class="title">@lang('payment.confirm-method-heading')</h2>
			<p class="subtitle">@lang('payment.confirm-method-lead')</p>
			<div class="columns">
				<div class="column">
					<form action="{{route('payment-confirm-order-post')}}" method="post">
						{!! csrf_field() !!}
						<input type="hidden" name="method" value="transfer"/>

						<a class="button is-primary is-outlined">@lang('payment.confirm-method-bank-transfer-button')</a>
					</form>
				</div>
				<div class="column">
					<form action="{{ config('przelewy24.transaction_url') }}" method="post" class="p24_form">
						<input type="hidden" name="p24_session_id" value="{{ $order->session_id }}"/>
						<input type="hidden" name="p24_merchant_id" value="{{ config('przelewy24.merchant_id') }}"/>
						<input type="hidden" name="p24_pos_id" value="{{ config('przelewy24.merchant_id') }}"/>
						<input type="hidden" name="p24_amount" value="{{ (int)$order->product->price * 100 }}"/>
						<input type="hidden" name="p24_currency" value="PLN"/>
						<input type="hidden" name="p24_description" value="{{ $order->product->name }}"/>
						<input type="hidden" name="p24_client" value="{{ $user->full_name }}"/>
						<input type="hidden" name="p24_address" value="{{ $user->address }}"/>
						<input type="hidden" name="p24_zip" value="{{ $user->zip }}"/>
						<input type="hidden" name="p24_city" value="{{ $user->city }}"/>
						<input type="hidden" name="p24_country" value="PL"/>
						<input type="hidden" name="p24_email" value="{{ $user->email }}"/>
						<input type="hidden" name="p24_language" value="pl"/>
						<input type="hidden" name="p24_url_return" value="{{ route('profile-orders') }}"/>
						<input type="hidden" name="p24_url_status" value="{{ route('payment-status-hook')  }} "/>
						<input type="hidden" name="p24_api_version" value="{{config('przelewy24.api_version')}}"/>
						<input type="hidden" name="p24_sign" value="{{ $checksum }}"/>

						<button class="button is-primary p24_submit">@lang('payment.confirm-method-online-payment-button')</button>
					</form>
				</div>
			</div>
		</section>
	</div>
@endsection