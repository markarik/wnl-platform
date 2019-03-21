@php
/**
 * @var App\Models\Coupon $coupon
 * @var App\Models\Order $order
 * @var App\Models\OrderInstalment[] $instalments
 * @var App\Models\PaymentMethod $paymentMethodInstalments
 * @var App\Models\User $user
 */
@endphp

@extends('layouts.checkout')

@section('content')
	<div class="t-checkout">
	@include('payment.cart', [
		'productName' => $order->product->name,
		'productPrice' => $order->product->price,
		'productAccessEnd' => $order->product->access_end,
		'productPriceWithCoupon' => $productPriceWithCoupon,
		'coupon' => $coupon,
	])

	<div class="t-checkout__content">
		@include('payment.stepper', ['currentStep' => 2])
		<section class="o-checkoutSection">
			<h2 class="o-checkoutSection__header">@lang('payment.confirm-order-heading')</h2>
			<form action="{{route('payment-confirm-order-post')}}" method="post" id="customPaymentForm">
				{!! csrf_field() !!}
				<input id="customPaymentMethodInput" type="hidden" name="method" value=""/>
			</form>

			<div class="o-paymentOptions">
				@if($order->coupon && (int) $order->total_with_coupon === 0)
					<div class="m-paymentOption -active">
						<div class="m-formGroup -checkbox -marginLess">
							<div>
								<label class="m-paymentOption__label">@lang('payment.confirm-order-payment-method-free-label')</label>
								<span class="m-paymentOption__info">@lang('payment.confirm-order-payment-method-free-info')</span>
							</div>
						</div>
						<i class="a-icon -medium fa-gift"></i>
					</div>
				@else
					<div class="m-paymentOption -active">
						<div class="m-formGroup -checkbox -marginLess">
							<input type="radio" name="payment_method" value="now" id="paymentMethodNow" class="a-radio" checked/>
							<div>
								<label for="paymentMethodNow" class="m-paymentOption__label">@lang('payment.confirm-order-payment-method-now-label')</label>
								<span class="m-paymentOption__info">@lang('payment.confirm-order-payment-method-now-info')</span>
							</div>
						</div>
						<i class="a-icon -medium fa-thumbs-up"></i>
					</div>

					<div class="m-paymentOption">
						<div class="m-formGroup -checkbox -marginLess">
							<input type="radio" name="payment_method" value="later" id="paymentMethod7days" class="a-radio"/>
							<div>
								<label for="paymentMethod7days" class="m-paymentOption__label">@lang('payment.confirm-order-payment-method-later-label')</label>
								<span class="m-paymentOption__info">@lang('payment.confirm-order-payment-method-later-info')</span>
							</div>
						</div>
						<i class="a-icon -medium fa-calendar"></i>
					</div>

					<form action="{{ config('przelewy24.transaction_url') }}" method="post" id="fullPaymentP24Form">
						<input type="hidden" name="p24_session_id" value="{{ $order->session_id }}"/>
						<input type="hidden" name="p24_merchant_id" value="{{ config('przelewy24.merchant_id') }}"/>
						<input type="hidden" name="p24_pos_id" value="{{ config('przelewy24.merchant_id') }}"/>
						<input type="hidden" name="p24_amount" value="{{ $amount }}"/>
						<input type="hidden" name="p24_currency" value="PLN"/>
						<input type="hidden" name="p24_description" value="{{ $order->product->name }}"/>
						<input type="hidden" name="p24_client" value="{{ $user->full_name }}"/>
						<input type="hidden" name="p24_address" value="{{ $user->userAddress->street}}"/>
						<input type="hidden" name="p24_zip" value="{{ $user->userAddress->zip }}"/>
						<input type="hidden" name="p24_city" value="{{ $user->userAddress->city }}"/>
						<input type="hidden" name="p24_country" value="PL"/>
						<input type="hidden" name="p24_email" value="{{ $user->email }}"/>
						<input type="hidden" name="p24_language" value="pl"/>
						<input type="hidden" name="p24_url_return" value="{{ $returnUrl }}"/>
						<input type="hidden" name="p24_url_status" value="{{ config('przelewy24.status_url') }}"/>
						<input type="hidden" name="p24_api_version" value="{{config('przelewy24.api_version')}}"/>
						<input type="hidden" name="p24_sign" value="{{ $checksum }}"/>
						<input type="hidden" name="p24_encoding" value="UTF-8"/>
					</form>
				@endif

				<div class="o-paymentOptions__instalments">
				@if ($instalments)
					<div class="m-formGroup -checkbox -withIcon">
						<input type="checkbox" name="instalments" id="instalments" class="a-checkbox"/>
						<label class="a-label" for="instalments">@lang('payment.confirm-order-payment-method-instalments-label')</label>
						<i class="a-icon -cadetBlue fa-info-circle -touchable" id="instalments-modal-opener"></i>

						<div id="instalments-modal" class="modal">
							<div class="modal-background"></div>
							<div class="modal-card">
								<section class="modal-card-body">
									@include('payment.instalments-modal', ['instalments' => $instalments, 'order' => $order])
								</section>
							</div>
						</div>

						<form action="{{ config('przelewy24.transaction_url') }}" method="post" id="instalmentsP24Form">
							<input type="hidden" name="p24_session_id" value="{{ $order->session_id }}"/>
							<input type="hidden" name="p24_merchant_id" value="{{ config('przelewy24.merchant_id') }}"/>
							<input type="hidden" name="p24_pos_id" value="{{ config('przelewy24.merchant_id') }}"/>
							<input type="hidden" name="p24_amount" value="{{ (int)$instalments[0]['amount'] * 100 }}"/>
							<input type="hidden" name="p24_currency" value="PLN"/>
							<input type="hidden" name="p24_description" value="{{ $order->product->name }}"/>
							<input type="hidden" name="p24_client" value="{{ $user->full_name }}"/>
							<input type="hidden" name="p24_address" value="{{ $user->userAddress->street}}"/>
							<input type="hidden" name="p24_zip" value="{{ $user->userAddress->zip }}"/>
							<input type="hidden" name="p24_city" value="{{ $user->userAddress->city }}"/>
							<input type="hidden" name="p24_country" value="PL"/>
							<input type="hidden" name="p24_email" value="{{ $user->email }}"/>
							<input type="hidden" name="p24_language" value="pl"/>
							<input type="hidden" name="p24_url_return" value="{{ url('app/myself/orders?payment') }}"/>
							<input type="hidden" name="p24_url_status" value="{{ config('przelewy24.status_url')  }}"/>
							<input type="hidden" name="p24_api_version" value="{{ config('przelewy24.api_version') }}"/>
							<input type="hidden" name="p24_sign" value="{{ $instalmentsChecksum }}"/>
							<input type="hidden" name="p24_encoding" value="UTF-8"/>
						</form>
					</div>
				@elseif (empty($paymentMethodInstalments))
					@lang('payment.confirm-order-instalments-not-available-for-product-text')
				@else
					@lang('payment.confirm-order-instalments-not-available-text')
				@endif
				</div>
			</div>

			@if ($order->has_shipment)
				<div>
					<h2 class="o-checkoutSection__subheader">@lang('payment.confirm-personal-data-address-header')</h2>
					<ul class="o-checkoutSection__dataReadOnly">
						<li>{{ $user->userAddress->recipient }}</li>
						<li>{{ $user->userAddress->street }}</li>
						<li>{{ $user->userAddress->zip }} {{ $user->userAddress->city }}</li>
					</ul>
					<a class="o-checkoutSection__editLink" href="{{ route('payment-personal-data') }}" data-link="edit-personal-data">@lang('payment.confirm-change-order')</a>
				</div>
			@endif

			<div>
				<h2 class="o-checkoutSection__subheader">@lang('payment.personal-data-invoice-heading')</h2>
				@if ($user->invoice)
					<ul class="o-checkoutSection__dataReadOnly">
						<li>{{ $user->invoice_name }}</li>
						<li>{{ $user->invoice_address }}</li>
						<li>{{ $user->invoice_zip }} {{ $user->invoice_city }}</li>
						<li>{{ $user->invoice_country }}</li>
						<li>@lang('payment.confirm-order-nip') {{ $user->invoice_nip }}</li>
					</ul>
				@else
					<ul class="o-checkoutSection__dataReadOnly">
						<li>{{ $user->first_name }} {{ $user->last_name }}</li>
						<li>{{ $user->userAddress->street }}</li>
						<li>{{ $user->userAddress->zip }} {{ $user->userAddress->city }}</li>
					</ul>
				@endif
				<a class="o-checkoutSection__editLink" href="{{ route('payment-personal-data') }}">@lang('payment.confirm-change-order')</a>
			</div>
			<button class="a-button -big" id="confirmOrderSubmit">@lang('payment.confirm-order-submit')</button>
		</section>
	</div>
	</div>
@endsection

@section('payment-scripts')
	<script>typeof fbq === 'function' && fbq('track', 'InitiateCheckout', {platform: '{{config('app.instance_name')}}'})</script>
@endsection
