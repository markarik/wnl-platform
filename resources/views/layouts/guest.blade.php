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
				<p>
					<small>
						<a class="tou-open-modal-link">
							@lang('payment.personal-data-tou-link-content')
						</a>
						&nbsp;|&nbsp;
						<a class="privacy-policy-open-modal-link">
							@lang('payment.personal-data-privacy-link-content')
						</a>
					</small>
				</p>
				<small>@lang('common.footer-copy')</small>
			</footer>
		</div>
		<div class="modals">
			<div id="tou-modal" class="modal">
				<div class="modal-background"></div>
				<div class="modal-card">
					<header class="modal-card-head">
						<p class="modal-card-title">@lang('payment.personal-data-tou-title')</p>
						<button class="delete"></button>
					</header>
					<section class="modal-card-body content">
						@include('payment.documents.tou')
					</section>
				</div>
			</div>
			<div id="privacy-policy-modal" class="modal">
				<div class="modal-background"></div>
				<div class="modal-card">
					<header class="modal-card-head">
						<p class="modal-card-title">@lang('payment.personal-data-privacy-title')</p>
						<button class="delete"></button>
					</header>
					<section class="modal-card-body content">
						@include('payment.documents.privacy-policy')
					</section>
				</div>
			</div>
		</div>
		<!-- Scripts -->
		@yield('scripts')
	</body>
</html>
