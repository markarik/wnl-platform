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
			<nav class="nav o-navigation">
				<div class="container">
					<div class="nav-left">
						<a class="nav-item" href="https://wiecejnizlek.pl">
							<img src="{{ asset('/images/wnl-logo.svg') }}" alt="Logo Więcej niż LEK">
						</a>
					</div>

					<div class="nav-right">
						<div class="nav-item">
							<span class="icon has-text-info -storm-grey">
								<i class="fa fa-shopping-cart a-icon -small"></i>
							</span>
						</div>
					</div>
				</div>
			</nav>

			@yield('content')
			<footer class="o-footer -white">
				<p class="o-footer__block -first">
					<span>
						Bezpieczna transakcja realizowana przez Przelewy24
					</span>
					<span class="icon has-text-info -mischka">
						<i class="fa fa-shield a-icon -medium"></i>
					</span>
				</p>
				<p class="o-footer__block">
					<span>
						W razie problemów napisz do nas na Messengerze lub wyślij maila na adres: <a href="mailto:info@wiecejnizlek.pl" class="-white -underline">info@wiecejnizlek.pl</a>
					</span>
				</p>
			</footer>
		</div>
		<!-- Scripts -->
		<script src="{{ mix('js/guest.js') }}"></script>
		@yield('scripts')
	</body>
</html>
