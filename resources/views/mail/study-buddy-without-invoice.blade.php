@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $user->first_name ?? '{first_name}' }}!</h3>

	<p style="font-size: 1.25em;">Twój Study Buddy dołączył właśnie do kursu!</p>

	<p>Spieszymy z informacją, że Twój unikalny kod promocyjny został wykorzystany! Dzięki temu obdarowana przez Ciebie osoba i Ty, otrzymujecie 100zł zniżki na kurs! Dziękujemy Ci za podzielenie się informacją o Więcej niż LEK!</p>

	<p>Cena Twojego zamówienia oraz wysokości przyszłych rat zostały odpowiednio obniżone. :)</p>

	<p>Jeżeli Twoje zamówienie zostało już opłacone w całości - w ciągu 14 dni dokonamy zwrotu na konto, z którego była wykonana płatność.</p>

	<p>Szczegóły dotyczące zamówienia znajdziesz w zakładce <a href="{{url('app/myself/orders')}}" target="_blank">KONTO > Twoje zamówienia</a>. :)</p>

	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/QbkL9WuorOlgI/giphy.gif" alt="Jak dobrze, że jesteś!" style="display: block; margin: 0 auto;">
	</p>

	<p>Z pozdrowieniami</p>
@endsection
