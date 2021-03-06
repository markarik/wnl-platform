@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $order->user->first_name ?? '{first_name}' }}!</h3>

	<p>W załączniku przesyłamy fakturę końcową, będącą potwierdzeniem dostarczenia i realizacji kursu. :)</p>

	<p>Faktura może Ci się przydać, aby uzyskać refundację z Izby Lekarskiej lub jeżeli prowadzisz działalność gospodarczą.</p>

	<p><strong>Uwaga! Przy płatności ratalnej, jeżeli w dniu wystawienia faktury nie masz w pełni opłaconego zamówienia, na fakturze końcowej będzie widniała zapłacona dotychczas kwota, a nie pełna. Do kolejnych wpłat nie możemy już wystawiać nowych faktur, dlatego składając dokumenty w Izbie dołącz potwierdzenia przelewów wykonanych po pierwszym dniu kursu. :)</strong></p>

	<p>Wszystkie faktury dotyczące Twoich zamówień znajdziesz w zakładce <a href="{{url('app/myself/orders')}}">KONTO > Twoje zamówienia</a>.</p>

	<p>Jeżeli szukasz certyfikatu uczestnictwa, znajdziesz go w zakładce <a href="{{url('app/myself/certificates')}}">KONTO > Certyfikaty</a>. :)</p>

	<p>Dziękujemy za uczestnictwo i życzymy powodzenia w nauce!</p>

	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/yUrUb9fYz6x7a/giphy.gif" alt="Tyle papierologii..." style="display: block; margin: 0 auto;">
	</p>
@endsection
