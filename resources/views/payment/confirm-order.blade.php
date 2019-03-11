@extends('payment.layout')

@section('content')

	@include('payment.cart', [
		'productName' => $order->product->name,
		'productPrice' => $order->product->price,
		'productAccessEnd' => $order->product->access_end,
		'hasCoupon' => $order->product->price !== $productPriceWithCoupon,
		'couponValue' => $couponValue,
		'productPriceWithCoupon' => $productPriceWithCoupon
	])

	@include('payment.payment-hero', [
		'step' => 3,
		'title' => trans('payment.confirm-order-title'),
		'subtitle' => trans('payment.confirm-order-subtitle'),
	])

	<div class="container">
		<section class="subsection">
			<div class="box">
				<p class="title">@lang('payment.confirm-order-heading')</p>
				<p class="subtitle">{{ $order->product->name }}</p>
				@if($order->coupon && ($order->coupon->slug !== 'wnl-online-only' || $order->product->slug === 'wnl-online'))
					<p class="strikethrough">
						@lang('payment.confirm-order-price', [ 'price' => $order->product->price ])
					</p>
					<p class="big strong">
						@lang('payment.confirm-order-price', [ 'price' => $order->total_with_coupon ])
					</p>
					<div class="notification margins top">
						@lang('payment.confirm-order-coupon', [
							'name' => $order->coupon->name,
							'value' => $order->coupon->value_with_unit,
						])
					</div>
				@else
					<p class="big strong">@lang('payment.confirm-order-price', [ 'price' => $order->product->price ])</p>
				@endif
			</div>
		</section>

		<section class="subsection">
			<div class="box">
				<p class="title">@lang('payment.confirm-personal-data-heading')</p>
				<p class="big">{{ $user->full_name }}</p>
				<p><strong>{{ $user->email }}</strong></p>

				<p class="margin top big">@lang('payment.confirm-personal-data-private')</p>

				@if(is_array($user->identityNumbers))
					<p>
						<strong>
							{{ trans('payment.identity_number_' . $user->identityNumbers[0]['type']) }}:
							{{ $user->identityNumbers[0]['value'] }}
						</strong>
					</p>
				@endif

				<p class="margin top big">@lang('payment.confirm-personal-data-address')</p>
				<ul>
					<li>{{ $user->userAddress->recipient }}</li>
					<li>{{ $user->userAddress->street }}</li>
					<li>{{ $user->userAddress->zip }}, {{ $user->userAddress->city }}</li>
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
			<p class="has-text-centered edit-personal-data">
				<a href="{{ route('payment-personal-data') }}?edit=true">@lang('payment.confirm-change-order')</a>
			</p>
		</section>

		@if($order->coupon && (int) $order->total_with_coupon === 0)
			<section class="subsection has-text-centered margin top">
				<p class="subtitle">
					@lang('payment.confirm-order-free')
				</p>
				<form action="{{route('payment-confirm-order-post')}}" method="post">
					{!! csrf_field() !!}
					<input type="hidden" name="method" value="free"/>

					<button class="button is-primary">
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
						<button class="button is-primary p24-submit" data-id="full_payment_p24_form"
						id="p24-submit-full-payment"
						data-payment="online">@lang('payment.confirm-method-online-payment-button')</button>
					</div>
					 <div class="column">
						<form action="{{route('payment-confirm-order-post')}}" method="post">
							{!! csrf_field() !!}
							<input type="hidden" name="method" value="online"/>

							<button type="submit" class="button">@lang('payment.confirm-deferred-payment-button')</button>
						</form>
					</div>
				</div>
			</section>
			@if($instalments)
				 <section class="has-text-centered">
					<div class="expandable">
						<div class="margin vertical">
							<a class="link expand" id="expand-instalments">Płatność na raty</a>
						</div>
						<div class="expandable-content box">
							<h4>Płatność w 3 ratach</h4>
							<p>Potrzebujesz rozłożyć płatność w czasie? Nie ma problemu!</p>
							<p class="margin bottom">Możesz zapłacić w trzech ratach - pierwszej <strong>7 dni po złożeniu zamówienia</strong> i kolejnych do <strong>20 listopada</strong> i <strong>20 grudnia</strong>.</p>

							<table class="table is-bordered margin vertical">
								<tr>
									<th>Twój wariant kursu</th>
									@foreach ($instalments as $instalment)
										<th>
											@if($loop->first)
												1. rata (do 7 dni po złożeniu zamówienia)
											@else
												{{$loop->index + 1}}. rata (do&nbsp;{{$instalment['date']->format('d.m.Y')}})
											@endif
										</th>
									@endforeach
									<th>Razem</th>
								</tr>
								<tr>
									<td>{{ $order->product->name }}</td>
									@for ($i = 0; $i < count($instalments); $i++)
										<th>
											{{ $instalments[$i]['amount'] }}zł
										</th>
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
									<button class="button is-primary p24-submit" data-id="instalments_p24_form"
									 data-payment="instalments">@lang('payment.confirm-method-instalments-online-button')</button>
								</div>

								<div class="column">
									<form action="{{route('payment-confirm-order-post')}}" method="post">
										{!! csrf_field() !!}
										<input type="hidden" name="method" value="instalments"/>
										<button type="submit" class="button margin top" id="instalments-button">
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
						Ze względu na zbliżający się start kursu, płatność na raty nie jest już dostępna.
					</div>
				</section>
			@endif
		@endif
	</div>
@endsection

@section('payment-scripts')
	<script>typeof fbq === 'function' && fbq('track', 'InitiateCheckout')</script>
@endsection
