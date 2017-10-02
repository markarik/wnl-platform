@extends('mail.newsletter.layout')

@section('content')

	<h3>Cześć %recipient_name%!</h3>

	<p><strong>Piszemy, aby przypomnieć o wypełnieniu ankiety z wynikami!</strong></p>

	<p>Zebraliśmy już ponad 50 odpowiedzi, ale aby móc podzielić się rzetelnymi statystykami potrzebujemy ich ok. 100! Jeśli masz ochotę i możliwość podzielić się z nami rezultatem, a nie było okazji, aby to zrobić - wypełnij ankietę już teraz! :) Podczas środowego streamingu o godz. 20:00 planujemy dzielić się wynikami, zatem im szybciej, tym lepiej! ;)</p>

	<div style="text-align: center; margin: 10px auto 20px;">
		<a class="button" href="https://goo.gl/forms/z15xkbbSoaW93Vjr2">
			Przejdź do ankiety
		</a>
	</div>

	<hr style="border: 0; height: 1px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));">

	<p>Do zobaczenia!</p>
@endsection
