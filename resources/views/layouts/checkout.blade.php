<!DOCTYPE html>
<html lang="en" class="styleguide">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta property="og:url" content="{{env('APP_URL')}}/payment/account">
		<meta property="og:type" content="website">
		<meta property="og:title" content="@lang('common.app-title')">
		<meta property="og:description" content="Zapisy otwarte!">
		<meta property="og:image" content="{{ asset('/images/fbogimage.jpg') }}">

		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>@lang('common.app-title')</title>

		<link rel="icon" href="{{ url('favicon.png') }}">

		<!-- Styles -->
		<link href="{{ mix('css/app.css') }}" rel="stylesheet">

		<!-- Scripts -->
		<script>
			window.Laravel = <?php echo json_encode([
					'csrfToken' => csrf_token(),
			]); ?>
		</script>
		@include('tracking')
	</head>
	<body data-base="{{ env('APP_URL') }}" class="styleguide">
		<form method="post" action="{{route('logout', ['redirectToRoute' => 'payment-account'])}}" id="logoutForm">
			{{ csrf_field() }}
		</form>
		<div class="t-app">
			<nav class="o-navigation">
				<a class="o-navigation__item" href="https://wiecejnizlek.pl">
					<img src="{{ asset('/images/wnl-logo.svg') }}" alt="Logo Więcej niż LEK">
				</a>

				<div class="o-navigation__right -stormGray">
					@if(empty($disableCart))
						<i class="o-navigation__item -hiddenMAndUp -touchable fa-shopping-cart a-icon -small" id="cartIcon"></i>
					@endif
					@php
					/** @var \App\Models\User $user */
					$user = Auth::user();
					@endphp
					@if ($user)
						<div class="o-navigation__item o-dropdown" id="accountDropdown">
							<div class="o-dropdown__trigger o-navigation__avatar" id="accountDropdownTrigger">
									@if (!empty($user->profile->avatar_url))
										<img src="{{$user->profile->avatar_url}}" class="a-avatar"/>
									@elseif (!empty($user->initials))
										<span class="a-avatar -automatic">
											{{$user->initials}}
										</span>
									@else
										<i class="o-navigation__item fa-user a-icon -small"></i>
									@endif
							</div>
							<div class="o-dropdown__menu -shadowLarge">
								{{-- logout link is identified by class because on some pages we have more than one logout link --}}
								<span class="o-dropdown__item logout-link">Wyloguj</span>
							</div>
						</div>
					@endif
				</div>
			</nav>

			@yield('content')
			<footer class="o-footer -white">
				<p class="o-footer__block -first">
					<span>@lang('payment.footer-safe-transaction')</span>
					<i class="fa-shield a-icon -medium -mischka"></i>
				</p>
				<p class="o-footer__block -last">
					<span>@lang('payment.footer-help')</span>
				</p>
			</footer>
		</div>
		<!-- Scripts -->
		<script src="{{ mix('js/payment.js') }}"></script>
		@yield('payment-scripts')
		@yield('scripts')
		@include('payment.facebook-messenger')
	</body>
</html>
