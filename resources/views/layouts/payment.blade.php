<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>{{ config('app.name', 'Laravel') }}</title>

		<!-- Styles -->
		<link href="/css/app.css" rel="stylesheet">

		<!-- Scripts -->
		<script>
			window.Laravel = <?php echo json_encode([
					'csrfToken' => csrf_token(),
			]); ?>
		</script>
	</head>
	<body data-base="{{env('APP_URL')}}">
		<div id="app">
			<nav class="navbar navbar-default navbar-static-top">
				<div class="container payment-container">
					<div class="navbar-header">

						<!-- Collapsed Hamburger -->
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
							<span class="sr-only">@lang('common.mobile-nav-toggle')</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>

						<!-- Branding Image -->
						<a class="navbar-brand" href="{{ route('home') }}">
							<img src="{{ asset('/images/wnl-logo.svg') }}" class="navbar-brand-image">
						</a>
					</div>

					<div class="collapse navbar-collapse" id="app-navbar-collapse">
						<!-- Left Side Of Navbar -->
						<ul class="nav navbar-nav">
							&nbsp;
						</ul>

						<!-- Right Side Of Navbar -->
						<ul class="nav navbar-nav navbar-right">
							<li><a href="@lang('common.course-website-link')">@lang('payment.back-to-website')</a></li>
						</ul>
					</div>
				</div>
			</nav>

			<div class="container payment-container">
				<div class="row">
					<div class="payment-content col-xs-12 col-xs-offset-0 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
						@yield('content')
					</div>
				</div>
			</div>
		</div>

		<!-- Scripts -->
		<script src="/js/payment.js"></script>
	</body>
</html>
