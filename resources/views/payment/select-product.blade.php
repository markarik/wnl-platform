@extends('layouts.payment')

@section('content')
<div class="row">
	<div class="col-xs-12 text-center">
		<h2>Witaj!</h2>
		<p class="lead">Wybierz kurs, na który chcesz się zapisać.</p>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-6">
		<h3>Kurs internetowy<br>+ stacjonarny</h3>
		<ul class="list-group">
			<li class="list-group-item">Uczestnictwo w 12-tygodniowym kursie</li>
			<li class="list-group-item">Dostęp do platformy e-learningowej</li>
			<li class="list-group-item">Autorskie materiały</li>
			<li class="list-group-item">Uczestnictwo w 6 warsztatach weekendowych w Poznaniu</li>
			<li class="list-group-item">Indywidualne spotkania z prowadzącymi kurs</li>
		</ul>
	</div>
	<div class="col-xs-12 col-md-6">
		<h3>Kurs internetowy<br>&nbsp;</h3>
		<ul class="list-group">
			<li class="list-group-item">Uczestnictwo w 12-tygodniowym kursie</li>
			<li class="list-group-item">Dostęp do platformy e-learningowej</li>
			<li class="list-group-item">Autorskie materiały</li>
		</ul>
	</div>
</div>
<div class="row text-center">
	<div class="col-xs-12 col-md-6">
		<a href="{{route('payment-personal-data', 'wnl-online-onsite')}}">Stacjonarny</a>
	</div>
	<div class="col-xs-12 col-md-6">
		<a href="{{route('payment-personal-data', 'wnl-online')}}">Online</a>
	</div>
</div>
@endsection