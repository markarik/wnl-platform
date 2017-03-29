@extends('layouts.guest')

@section('content')
	<section class="hero is-medium">
		<div class="hero-body">
			<div class="container has-text-centered">
				<p class="title">Ups, wygląda na to, że nie ma strony o takim adresie</p>
				<p class="subtitle">Gdzie chcesz iść teraz?</p>
				<p class="margin vertical">
					<a class="margin horizontal" href="{{ config('app.url') }}">Strona główna platformy</a>
					<a class="margin horizontal" href="@lang('common.course-website-link')">Strona kursu "Więcej niż LEK"</a>
				</p>
				<img src="https://media.giphy.com/media/3o7aTskHEUdgCQAXde/giphy.gif" alt="Vincent Vega szuka">
				<p class="margin top">Jeżeli sami skierowaliśmy Cię tutaj - prosimy, daj nam znać, żebyśmy mogli to naprawić. Dzięki!</p>
				<p><small>nerdy@wiecejnizlek.pl</small></p>
			</div>
		</div>
	</section>
@endsection
