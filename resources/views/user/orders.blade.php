@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Your orders</div>

					<div class="panel-body">
						@foreach($orders as $order)

							<strong>@lang('profile.order') #{{ $order->id }} {{ $order->product->name }}</strong>

							@if($order->paid)
								@lang('profile.order-paid');
							@endif

							@if(!$order->paid && $order->method == 'transfer')
								@lang('profile.bank-account')
							@endif

							@if(!$order->paid && $order->method == 'online')
								<div class="order-pending-notification" data-id="{{ $order->id }}">
									@lang('profile.payment-pending')<br>
									<img src="{{ asset('images/loader.svg') }}" id="loader-{{ $order->id }}">
									<br>
									@lang('profile.contact-us-if-payment-failed')
								</div>
							@endif
						@endforeach

						@if(!$order->paid)
							<br>
							<a href="{{ route('payment-confirm-order') }}" class="button"
							   id="change-method-button-{{ $order->id }}">
								@lang('profile.change-payment-method')
							</a>
						@endif

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
