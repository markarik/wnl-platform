@php
/**
 * @var App\Models\Coupon $coupon
 * @var App\Models\Order $order
 * @var App\Models\OrderInstalment[] $instalments
 * @var App\Models\PaymentMethod $paymentMethodInstalments
 * @var App\Models\User $user
 */
@endphp

@extends('payment.layout')

@section('content')

	@include('payment.cart', [
		'productName' => $order->product->name,
		'productPrice' => $order->product->price,
		'productAccessEnd' => $order->product->access_end,
		'productPriceWithCoupon' => $productPriceWithCoupon,
		'coupon' => $coupon,
	])

	<div class="payment-content t-checkout__content">
		<section class="o-checkoutSection">
			<h2 class="o-checkoutSection__header">@lang('payment.confirm-order-heading')</h2>
			{{-- TODO payment options--}}

			@if ($order->has_shipment)
				<h2 class="o-checkoutSection__subheader">@lang('payment.confirm-personal-data-address-header')</h2>
				<ul class="o-checkoutSection__info">
					<li>{{ $user->userAddress->recipient }}</li>
					<li>{{ $user->userAddress->street }}</li>
					<li>{{ $user->userAddress->zip }} {{ $user->userAddress->city }}</li>
				</ul>
				<a class="o-checkoutSection__editLink" href="{{ route('payment-personal-data') }}">@lang('payment.confirm-change-order')</a>
			@endif

			<h2 class="o-checkoutSection__subheader">@lang('payment.confirm-personal-data-address-header')</h2>
			@if($user->invoice)
				<ul class="o-checkoutSection__info">
					<li><strong>{{ $user->invoice_name }}</strong></li>
					<li>{{ $user->invoice_address }}</li>
					<li>{{ $user->invoice_zip }} {{ $user->invoice_city }}</li>
					<li>{{ $user->invoice_country }}</li>
					<li>@lang('payment.confirm-order-nip') {{ $user->invoice_nip }}</li>
				</ul>
			@else
				<ul class="o-checkoutSection__info">
					<li><strong>{{ $user->first_name }} {{ $user->last_name }}</strong></li>
					<li>{{ $user->userAddress->street }}</li>
					<li>{{ $user->userAddress->zip }} {{ $user->userAddress->city }}</li>
				</ul>
			@endif
			<a class="o-checkoutSection__editLink" href="{{ route('payment-personal-data') }}">@lang('payment.confirm-change-order')</a>
		</section>

		@if($order->coupon && (int) $order->total_with_coupon === 0)
			<section class="subsection has-text-centered margin top">
				<p class="subtitle">
					@lang('payment.confirm-order-free')
				</p>
				<form action="{{route('payment-confirm-order-post')}}" method="post">
					{!! csrf_field() !!}
					<input type="hidden" name="method" value="free"/>

					<button class="a-button -big" data-button="pay-free">
						@lang('payment.confirm-method-free-button')
					</button>
				</form>
			</section>
		@else
			<section class="subsection has-text-centered margin top">
				{{-- <h2 class="title">@lang('payment.confirm-method-heading')</h2> --}}
				<p class="subtitle">@lang('payment.confirm-method-heading')</p>
				<div class="columns margin top">
					<div class="column">
						<form action="{{ config('przelewy24.transaction_url') }}" method="post" class="p24_form" id="full_payment_p24_form">
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
						<button class="a-button -big p24-submit" data-id="full_payment_p24_form"
						id="p24-submit-full-payment"
                        data-button="pay-online-now"
						data-payment="online">@lang('payment.confirm-method-online-payment-button')</button>
					</div>
					 <div class="column">
						<form action="{{route('payment-confirm-order-post')}}" method="post">
							{!! csrf_field() !!}
							<input type="hidden" name="method" value="online"/>

							<button type="submit" class="a-button -big -secondary" data-button="pay-online-later">@lang('payment.confirm-deferred-payment-button')</button>
						</form>
					</div>
				</div>
			</section>
			@if ($instalments)
				 <section class="has-text-centered">
					<div class="expandable">
						<div class="margin vertical">
							<a class="link expand" id="expand-instalments">Płatność na raty</a>
						</div>
						<div class="expandable-content box">
							<h4>Płatność w 3 ratach</h4>
							<p>Potrzebujesz rozłożyć płatność w czasie? Nie ma problemu!</p>
							<p class="margin bottom">
								Możesz zapłacić w trzech ratach - pierwszej <strong>7 dni po złożeniu zamówienia</strong>
								i kolejnych do <strong data-date-format="D MMMM" data-timestamp="{{$instalments[1]->due_date->timestamp}}">{{$instalments[1]->due_date->format('d.m.Y')}}</strong>
								i <strong data-date-format="D MMMM" data-timestamp="{{$instalments[2]->due_date->timestamp}}">{{$instalments[2]->due_date->format('d.m.Y')}}</strong>.</p>

							<table class="table is-bordered margin vertical">
								<tr>
									<th>Twój wariant kursu</th>
									@foreach ($instalments as $instalment)
										<th>
											@if($loop->first)
												1. rata (do 7 dni po złożeniu zamówienia)
											@else
												{{$loop->index + 1}}. rata (do&nbsp;{{$instalment->due_date->format('d.m.Y')}})
											@endif
										</th>
									@endforeach
									<th>Razem</th>
								</tr>
								<tr id="instalments-amounts">
									<td>{{ $order->product->name }}</td>
									@for ($i = 0; $i < count($instalments); $i++)
										<td>
											{{ $instalments[$i]['amount'] }}zł
										</td>
									@endfor
									<td>{{ $order->total_with_coupon }}zł</td>
								</tr>
							</table>

							<p class="margin vertical has-text-centered">
								Więcej informacji na temat rat znajdziesz w <a href="https://wiecejnizlek.pl/cennik">Cenniku</a> oraz w <a class="tou-open-modal-link">Regulaminie</a>.
							</p>

							<div class="columns margin top">

								<div class="column">
									<form action="{{ config('przelewy24.transaction_url') }}" method="post" class="p24_form" id="instalments_p24_form">
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
									<button class="button is-primary p24-submit" data-id="instalments_p24_form" data-button="pay-instalments-now"
									 data-payment="instalments">@lang('payment.confirm-method-instalments-online-button')</button>
								</div>

								<div class="column">
									<form action="{{route('payment-confirm-order-post')}}" method="post">
										{!! csrf_field() !!}
										<input type="hidden" name="method" value="instalments"/>
										<button type="submit" class="button margin top" data-button="pay-instalments-later">
											@lang('payment.confirm-method-instalments-button')
										</button>
									</form>
								</div>

							</div>

						</div>
					</div>
				</section>
			@else
				<section class="has-text-centered">
					<div class="strong margin top">
						@if (empty($paymentMethodInstalments))
							Płatność na raty nie jest dostępna dla tego produktu.
						@else
							Ze względu na zbliżający się start kursu, płatność na raty nie jest już dostępna.
						@endif
					</div>
				</section>
			@endif
		@endif
	</div>
@endsection

@section('payment-scripts')
	<script>typeof fbq === 'function' && fbq('track', 'InitiateCheckout')</script>
@endsection
