<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta property="og:url" content="{{env('APP_URL')}}/login">
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
	<body data-base="{{ env('APP_URL') }}">
		<div id="app">
			<nav class="nav has-shadow">
				<div class="container">
					<div class="nav-left">
						<a class="nav-item" href="https://wiecejnizlek.pl">
							<img src="{{ asset('/images/wnl-logo.svg') }}" alt="Logo Więcej niż LEK">
						</a>
					</div>

					<span class="nav-toggle">
						<span></span>
						<span></span>
						<span></span>
					</span>

					<div class="nav-right nav-menu">
						<form method="post" action="{{route('logout', ['redirectToRoute' => $logoutRedirectToRoute ?? null])}}" id="logout-form">
							{{ csrf_field() }}
						</form>
						<a href="{{ url('payment/account') }}" class="nav-item">
							Zapisz się na kurs
						</a>
						<a href="@lang('common.course-website-link')" class="nav-item">
							@lang('payment.back-to-website')
						</a>
						@if (Auth::check())
							<a href="{{url('app/')}}" class="nav-item">
								Platforma
							</a>
							<a href="{{url('app/myself/orders')}}" class="nav-item">
								Twoje zamówienia
							</a>
							<a href="#" class="nav-item logout-link">
								Wyloguj się
							</a>
						@else
							<a href="{{url('login')}}" class="nav-item">
								Zaloguj się
							</a>
						@endif
					</div>
				</div>
			</nav>

			@yield('content')
			<footer class="footer has-text-centered">
				<p>
					<small>
						{{-- <a class="tou-open-modal-link"> --}}
						<a target="_blank" href="@lang('payment.tou-link-href')">
							@lang('payment.personal-data-tou-link-content')
						</a>
						&nbsp;|&nbsp;
						<a target="_blank" href="@lang('payment.privacy-policy-link-href')">
							@lang('payment.personal-data-privacy-link-content')
						</a>
					</small>
				</p>
				<p>
					<small>
						<a target="_blank" href="https://wiecejnizlek.pl/documents/RegulaminPromocjiStudyBuddy.pdf">
							Regulamin Promocji "Study Buddy"
						</a>
						&nbsp;|&nbsp;
						<a target="_blank" href="https://wiecejnizlek.pl/documents/RegulaminPromocjiUczestnikPoprzedniejEdycji.pdf">
							Regulamin Promocji "Uczestnik poprzedniej edycji"
						</a>
					</small>
				</p>
				<small>@lang('common.footer-copy')</small>
			</footer>
		</div>
		<div class="modals">
			@if(request()->route())
			<div id="login-modal" class="modal">
				<div class="modal-background"></div>
				<div class="modal-card">
					<header class="modal-card-head">
						<p class="modal-card-title"></p>
						<button class="delete"></button>
					</header>
					<section class="modal-card-body content">
						@include('auth.login-modal')
					</section>
				</div>
			</div>
			@endif
		</div>
		<!-- Scripts -->
		<script src="{{ mix('js/guest.js') }}"></script>
		@yield('scripts')
	</body>
</html>
