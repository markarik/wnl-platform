@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $order->user->first_name or '{first_name}' }}!</h3>

	<p>W załączniku przesyłamy fakturę końcową, będącą potwierdzeniem dostarczenia i realizacji kursu. :)</p>

	<p>Faktura może Ci się przydać, aby uzyskać refundację z Izby Lekarskiej lub jeżeli prowadzisz działalność gospodarczą.</p>

	<p>Dziękujemy za uczestnictwo i życzymy powodzenia w nauce!</p>

	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/yUrUb9fYz6x7a/giphy.gif" alt="Tyle papierologii..." style="display: block; margin: 0 auto;">
	</p>
@endsection
