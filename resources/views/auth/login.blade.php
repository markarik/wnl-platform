@extends('layouts.guest')

@section('content')

<div class="wnl-login-view">
	<div class="wnl-login-container">
		<h2 class="wnl-login-title">Cześć, dobrze Cię widzieć!</h2>
		<p class="wnl-login-subtitle">Aby rozpocząć, zaloguj się podając swój e-mail i hasło.</p>
		<form class="wnl-login-form" action="{{ url('/login') }}" method="post">
			{{ csrf_field() }}

			{{-- E-Mail --}}
			<label for="email" class="label">Twój e-mail</label>
			<p class="control">
				<input id="email" name="email" type="email"
					class="input {{ $errors->has('email') ? 'is-danger' : '' }}"
					value="{{ old('email') }}" required autofocus="">
				@if ($errors->has('email'))
					<span class="help is-danger">{{ $errors->first('email') }}</span>
				@endif
			</p>

			{{-- Password --}}
			<label for="password" class="label">Twoje hasło</label>
			<p class="control">
				<input id="password" name="password" type="password"
					class="input {{ $errors->has('password') ? 'is-danger' : '' }}"
					value="{{ old('password') }}" required autofocus="">
				@if ($errors->has('password'))
					<span class="help is-danger">{{ $errors->first('password') }}</span>
				@endif
			</p>

			{{-- Remember --}}
			<p class="control">
				<label class="checkbox">
					<input type="checkbox" name="remember">
					Zapamiętaj mnie
				</label>
			</p>

			{{-- Submit --}}
			<p class="control">
				<button type="submit" class="button is-primary is-wide">
					Zaloguj mnie
				</button>
			</p>

			{{-- Forgot password --}}
			<p class="control wnl-login-remind">
				<a href="{{ url('/password/reset') }}">
					Nie pamiętasz hasła?
				</a>
			</p>
		</form>
	</div>
</div>

@endsection
