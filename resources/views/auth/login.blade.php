@extends('layouts.guest')

@section('content')

<div class="wnl-login-view">
	<div class="wnl-login-container">
		<h2 class="wnl-login-title">@lang('auth.title')</h2>
		<p class="wnl-login-subtitle">@lang('auth.subtitle')</p>
		<form class="wnl-login-form" action="{{ url('/login') }}" method="post">
			{{ csrf_field() }}

			{{-- E-Mail --}}
			<label for="email" class="label">Twoje Imię</label>
			<p class="control">
				<input id="email" name="first_name" type="text"
					class="input {{ $errors->has('email') ? 'is-danger' : '' }}"
					value="{{ old('email') }}" required autofocus="">
				@if ($errors->has('email'))
					<span class="help is-danger">{{ $errors->first('email') }}</span>
				@endif
			</p>

			{{-- Password --}}
			<label for="password" class="label">Twoje Nazwisko (może być zmyślone)</label>
			<p class="control">
				<input name="last_name" type="text"
					class="input {{ $errors->has('password') ? 'is-danger' : '' }}"
					value="{{ old('password') }}" required autofocus="">
				@if ($errors->has('password'))
					<span class="help is-danger">{{ $errors->first('password') }}</span>
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
