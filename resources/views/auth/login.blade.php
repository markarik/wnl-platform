@extends('layouts.guest')

@section('content')

	<div class="wnl-login-view">
		<div class="wnl-login-container">
			@if($signupsOpen)
				<div class="notification is-success has-text-centered">
					<p class="strong">Odkryj naukę na nowo!</p>
					<p>Zapisy już trwają!</p>
					<p>
						<a href="{{ url('/payment/account') }}" class="button is-small is-success is-inverted">
							<span>Zapisz się na kurs</span>
							<span class="icon is-small"><i class="fa fa-thumbs-o-up"></i></span>
						</a>
					</p>
				</div>
			@endif
			@if(session('logout'))
				<div class="notification has-text-centered">Wylogowano, do zobaczenia!</div>
			@endif
			<p class="title is-3 is-hidden-touch">@lang('auth.title')</p>
			<p class="title is-5 is-hidden-desktop">@lang('auth.title')</p>
			<p class="wnl-login-subtitle">@lang('auth.subtitle')</p>
			<form class="wnl-login-form" action="{{ url('/login') }}" method="post">
				{{ csrf_field() }}

				{{-- E-Mail --}}
				<label for="email" class="label">@lang('auth.label-email')</label>
				<p class="control">
					<input id="email" name="email" type="email"
						class="input {{ $errors->has('email') ? 'is-danger' : '' }}"
						value="{{ old('email') }}" required autofocus="">
					@if ($errors->has('email'))
						<span class="help is-danger pre-line">{{ $errors->first('email') }}</span>
					@endif
				</p>

				{{-- Password --}}
				<label for="password" class="label">@lang('auth.label-password')</label>
				<p class="control">
					<input id="password" name="password" type="password"
						   class="input {{ $errors->has('password') ? 'is-danger' : '' }}"
						   value="{{ old('password') }}" required>
					@if ($errors->has('password'))
						<span class="help is-danger pre-line">{{ $errors->first('password') }}</span>
					@endif
				</p>

				{{-- Remember --}}
				<p class="control">
					<label class="checkbox">
						<input type="checkbox" name="remember">
						@lang('auth.label-remember')
					</label>
				</p>

				{{-- Submit --}}
				<p class="control">
					<button type="submit" class="button is-primary is-wide">
						@lang('auth.submit')
					</button>
				</p>

				{{-- Forgot password --}}
				<p class="control wnl-login-remind">
					<a href="{{ url('/password/reset') }}">
						@lang('auth.remind-link')
					</a>
				</p>
			</form>
		</div>
	</div>

@endsection
