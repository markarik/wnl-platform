@extends('payment.layout')

@section('content')

@include('payment.payment-hero', [
	'step' => 1,
	'title' => trans('payment.select-product-title'),
	'subtitle' => trans('payment.select-product-subtitle'),
])

<section class="section select-product">
	<div class="container">
		@if(!$product || $product->signups_close->isPast())
			<div class="column">
				<div class="notification has-text-centered strong">
					Zapisy zostały zakończone. <a href="https://wiecejnizlek.pl/rezerwacja/">Kliknij i zostaw swój e-mail</a>, aby zarezerwować miejsce na kolejnej edycji!
				</div>
			</div>
		@endif
		@if($product->signups_start->isFuture())
			<div class="notification has-text-centered strong">
				Do otwarcia zapisów pozostało:
				<div class="signups-countdown" data-start="{{ $product->signups_start->timestamp }}">
					sprawdzam zegarek...
				</div>
			</div>
			<div class="notification has-text-centered strong">
				<a href="https://wiecejnizlek.pl/rezerwacja/">Kliknij i zostaw swój e-mail</a>, aby zarezerwować miejsce na kolejnej edycji!
			</div>
		@endif
		@if(!$product->available)
			<div class="column">
				<div class="notification has-text-centered strong">
					Brak miejsc :(<br> <a href="https://wiecejnizlek.pl/rezerwacja/">Kliknij i zostaw swój e-mail</a>, aby zarezerwować miejsce na kolejnej edycji!
				</div>
			</div>
		@endif
	</div>
</section>
@endsection
