@extends('layouts.guest')

@section('content')

<div class="wnl-login-view">
	<div class="wnl-login-container">
		@if(session('logout'))
			<div class="notification has-text-centered">Wylogowano, do zobaczenia!</div>
		@endif
		<h2 class="wnl-login-title">Witaj na <strong>wersji demo</strong> platformy "Więcej niż LEK"</h2>
		<p class="wnl-login-subtitle">Aby rozpocząć, podaj Imię i Nazwisko, mogą być zmyślone. :)</p>
		<form class="wnl-login-form" action="{{ url('/login') }}" method="post">
			{{ csrf_field() }}

			{{-- First name, last name --}}
			<label for="email" class="label">Imię i nazwisko</label>
			<p class="control has-text-centered">
				<input id="email" name="first_name" type="text"
					class="input {{ $errors->has('email') ? 'is-danger' : '' }}"
					value="{{ old('email') }}" required autofocus="">
				@if ($errors->has('email'))
					<span class="help is-danger pre-line">{{ $errors->first('email') }}</span>
				@endif
			</p>

			{{-- Submit --}}
			<p class="control">
				<button type="submit" class="button is-primary is-wide">
					@lang('auth.submit')
				</button>
			</p>

		</form>
	</div>
</div>

@endsection
