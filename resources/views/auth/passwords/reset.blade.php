@extends('layouts.guest')

@section('content')

	<div class="wnl-login-view">
		<div class="wnl-login-container">
			@if (session('status'))
				<div class="notification is-success">
					{{ session('status') }}
				</div>
			@endif
			<p class="title is-3 is-hidden-touch">Podaj nowe hasło</p>
			<p class="title is-5 is-hidden-desktop">Podaj nowe hasło</p>
			<p class="wnl-login-subtitle">Dla pewności, podaj jeszcze raz swój adres e-mail, a następnie nowe hasło</p>
			<form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
				{{ csrf_field() }}

				<input type="hidden" name="token" value="{{ $token }}">

				<p class="control">
					<label for="email" class="label">Twój adres e-mail</label>
					<input id="email" type="email" class="input" name="email" value="{{ $email ?? old('email') }}" required autofocus class="{{ $errors->has('password_confirmation') ? ' is-danger' : '' }}">

					@if ($errors->has('email'))
						<span class="help is-danger pre-line">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</p>

				<p class="control">
					<label class="label" for="password">Nowe hasło</label>
					<input id="password" type="password" class="input {{ $errors->has('password') ? ' is-danger' : '' }}" name="password" required>

					@if ($errors->has('password'))
						<span class="help is-danger pre-line">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
				</p>

				<p class="control">
					<label for="password-confirm" class="label">Potwierdź nowe hasło</label>
					<input id="password-confirm" type="password" class="input {{ $errors->has('password') ? ' is-danger' : '' }}" name="password_confirmation" required>

					@if ($errors->has('password_confirmation'))
						<span class="help is-danger pre-line">
							<strong>{{ $errors->first('password_confirmation') }}</strong>
						</span>
					@endif
				</p>

				<p class="control">
					<button type="submit" class="button is-primary is-wide">
						Zapisz nowe hasło
					</button>
				</p>
			</form>
		</div>
	</div>
@endsection
