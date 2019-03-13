@extends('mail.layout')

@section('content')
	<h3>Witaj ponownie {{ $order->user->first_name ?? '{first_name}' }}!</h3>

	<p>Przypominamy, że zbliża się termin płatności {{ $instalment->order_number or '{left_amount}' }}. raty za <strong>{{$order->product->name or '{product_name}'}}</strong>. 🙂</p>

	<p>Pozostała kwota raty wynosi: <strong>{{ $instalment->left_amount ?? '{left_amount}' }}zł</strong></p>

	<p>Ratę możesz opłacić w zakładce <a href="{{url('app/myself/orders')}}" target="_blank">KONTO > Twoje zamówienia</a>.</p>

	<p class="has-text-centered" style="margin: 20px 0;">
		<a href="{{url('app/myself/orders')}}" class="button">
			ZAPŁAĆ KOLEJNĄ RATĘ
		</a>
	</p>

	@if($instalment->order_number === 1)
		<p>Brak opłacenia pierwszej raty spowoduje anulowanie zamówienia.</p>
	@else
		<p>Pamiętaj, że brak opłacenia raty w terminie będzie się wiązał z <strong>zawieszeniem dostępu do platformy</strong>. Zostanie on przywrócony zaraz po poprawnym zakończeniu płatności. 😉</p>
	@endif

	<p>W razie pytań pisz śmiało na info@wiecejnizlek.pl! 🙂</p>

	<p>Do zobaczenia!</p>
@endsection
