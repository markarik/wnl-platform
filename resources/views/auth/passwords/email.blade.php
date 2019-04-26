@extends('layouts.guest')

<!-- Main Content -->
@section('content')

	<div class="wnl-login-view">
		<div class="wnl-login-container">
			@if (session('status'))
				<div class="notification is-info has-text-centered">
					<p class="strong">{{ session('status') }}</p>
				</div>
			@endif
			<p class="title is-3 is-hidden-touch">Zmień swoje hasło</p>
			<p class="title is-5 is-hidden-desktop">Zmień swoje hasło</p>
			<p class="wnl-login-subtitle">Nie pamiętasz hasła? Żaden problem! Zaraz to naprawimy. :)</p>
			<form role="form" method="POST" action="{{ url('/password/email') }}">
				{{ csrf_field() }}

				<p class="control">
					<label for="email" class="label">Podaj swój adres e-mail</label>
					<input id="email" type="email" class="input {{ $errors->has('email') ? ' is-danger' : '' }}" name="email" value="{{ old('email') }}" autofocus="" required>

					@if ($errors->has('email'))
						<span class="help is-danger pre-line">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</p>

				<p class="control">
					<button type="submit" class="button is-primary is-wide">
						Resetuję hasło
					</button>
				</p>
			</form>
			<p class="has-text-centered">
				<small>W razie problemów pisz śmiało na info@wiecejnizlek.pl</small>
			</p>
		</div>
	</div>
@endsection
