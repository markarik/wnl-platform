@extends('layouts.payment')

@section('content')

<div class="container payment-container">
	<div class="row">
		<div class="col-xs-12 col-xs-offset-0 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="text-center">Czy wszystko się zgadza?</h2>

					<div class="panel panel-default">
						<div class="panel-heading">@lang('payment.personal-data-order-heading')</div>
						<div class="panel-body">
							<p class="lead">Więcej niż LEK - Kurs internetowy + stacjonarny</p>
							<p>2000zł netto</p>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">@lang('payment.personal-data-basic-heading')</div>
						<div class="panel-body">
							<p class="lead">{{$order['email']}}</p>
							<ul class="list-unstyled">
								<li><strong>{{$order['client']}}</strong></li>
								<li>{{$order['address']}}</li>
								<li>{{$order['zip']}}, {{$order['city']}}</li>
							</ul>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">@lang('payment.personal-data-invoice-heading')</div>
						<div class="panel-body">
							<ul class="list-unstyled">
								<li><strong>bethink s.c.</strong></li>
								<li>{{$order['address']}}</li>
								<li>{{$order['zip']}}, {{$order['city']}}</li>
								<li>Polska</li>
							</ul>
						</div>
					</div>

					<div>
						<p class="text-center">
							<a href="{{ url('/payment/step2') }}">@lang('payment.edit-account')</a>
						</p>
					</div>
				</div>
			</div>

			<hr>

			<div class="row text-center">
				<div class="col-xs-12">
					<h2>Jeżeli tak, to wszystko gotowe!</h2>
					<p class="lead">Kliknij&nbsp;na&nbsp;jeden&nbsp;z&nbsp;przycisków, aby&nbsp;wybrać&nbsp;metodę&nbsp;płatności&nbsp;i&nbsp;złożyć&nbsp;zamówienie.</p>
				</div>
				<div class="col-xs-12 col-md-6">
					<form action="{{url('/payment/step3')}}" method="post">
						{!! csrf_field() !!}
						<input type="hidden" name="method" value="transfer" />
						<button class="btn btn-default">@lang('payment.bank-transfer-button')</button>
					</form>
				</div>
				<div class="col-xs-12 col-md-6">
					<form action="{{ config('przelewy24.transaction_url') }}" method="post" class="p24_form">

						<input type="hidden" name="p24_session_id" value="{{$order['session_id']}}"/>
						<input type="hidden" name="p24_merchant_id" value="{{$order['merchant_id']}}"/>
						<input type="hidden" name="p24_pos_id" value="{{$order['merchant_id']}}"/>
						<input type="hidden" name="p24_amount" value="{{$order['amount']}}"/>
						<input type="hidden" name="p24_currency" value="{{$order['currency']}}"/>
						<input type="hidden" name="p24_description" value="{{$order['description']}}"/>
						<input type="hidden" name="p24_client" value="{{$order['client']}}"/>
						<input type="hidden" name="p24_address" value="{{$order['address']}}"/>
						<input type="hidden" name="p24_zip" value="{{$order['zip']}}"/>
						<input type="hidden" name="p24_city" value="{{$order['city']}}"/>
						<input type="hidden" name="p24_country" value="{{$order['country']}}"/>
						<input type="hidden" name="p24_email" value="{{$order['email']}}"/>
						<input type="hidden" name="p24_language" value="{{$order['language']}}"/>
						<input type="hidden" name="p24_url_return" value="{{$order['url_return']}}"/>
						<input type="hidden" name="p24_url_status" value="{{$order['url_status']}}"/>
						<input type="hidden" name="p24_api_version" value="{{config('przelewy24.api_version')}}"/>
						<input type="hidden" name="p24_sign" value="{{$order['sign']}}"/>

					</form>

					<button class="btn btn-primary p24_submit">@lang('payment.online-payment-button')</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection