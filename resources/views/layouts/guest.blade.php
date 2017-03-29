<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>@lang('common.app-title')</title>

		<!-- Styles -->
		<link href="/css/app.css" rel="stylesheet">

		<!-- Scripts -->
		<script>
			window.Laravel = <?php echo json_encode([
					'csrfToken' => csrf_token(),
			]); ?>
		</script>
	</head>
	<body data-base="{{ env('APP_URL') }}">
		<div id="app">
			<nav class="nav has-shadow">
				<div class="container">
					<div class="nav-left">
						<a class="nav-item" href="{{ route('home') }}">
							<img src="{{ asset('/images/wnl-logo.svg') }}" alt="Logo Więcej niż LEK">
						</a>
					</div>

					<div class="nav-right">
						<a href="@lang('common.course-website-link')" class="nav-item">
							@lang('payment.back-to-website')
						</a>
					</div>
				</div>
			</nav>

			@yield('content')
			<footer class="footer has-text-centered">
				<small>@lang('common.footer-copy')</small>
			</footer>
		</div>
		<!-- Scripts -->
		@yield('scripts')
	</body>
</html>
