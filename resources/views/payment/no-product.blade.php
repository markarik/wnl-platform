@extends('payment.layout')

@section('content')

@include('payment.payment-hero', [
	'step' => 1,
	'title' => trans('payment.select-product-title'),
	'subtitle' => trans('payment.select-product-subtitle'),
])

<section class="section select-product">
	<div class="container">
		<div class="column">
			<div class="notification has-text-centered strong">
				Zapisy zostały zakończone. <a href="https://wiecejnizlek.pl/zostaw-e-mail">Kliknij i zostaw swój e-mail</a>, aby zarezerwować miejsce na kolejnej edycji!
			</div>
		</div>

	</div>
</section>
@include('payment.info-links')
@endsection
