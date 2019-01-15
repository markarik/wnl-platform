@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $user->first_name ?? '{first_name}' }}!</h3>

	<p>Chcieliśmy pogratulować Ci długiej i systematycznej pracy do LEK-u! Jesteśmy pod wielkim wrażeniem motywacji, którą udało Ci się utrzymać!</p>

	<p>Dziękujemy Ci za udział kursie, który dobiega już końca. W załączniku znajdziesz certyfikat ukończenia kursu. 🙂 Jeżeli planujesz uzyskać refundację kursu z Izby Lekarskiej, to na pewno okaże się przydatny. 😉</p>

	<p>Kończąc, mamy do Ciebie skromną prośbę o podzielenie się swoją oceną kursu na <a href="https://www.facebook.com/pg/wiecejnizlek/reviews/" target="_blank">facebooku</a> oraz wypełnienie <a href="https://goo.gl/forms/C1mQ0MUwUzZBJyTO2">ostatniej ankiety ewaluacyjnej</a>. Rozumiemy, że teraz pewnie Twoje myśli krążą wokół nauki, ale może ten mail przypomni Ci o naszej prośbie po egzaminie. 🙂 </p>

	<p>Jeszcze raz gratulujemy Ci wytrwałości i życzymy powodzenia, zarówno na ostatniej prostej, jak i samym egzaminie!</p>

	<p>Do zobaczenia!</p>
@endsection
