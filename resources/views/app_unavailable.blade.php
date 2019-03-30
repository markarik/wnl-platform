<!DOCTYPE html>
<html>
<head>
	<title>Demo Więcej niż LEK - zaraz wracamy!</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.4.0/css/bulma.min.css">

	<style>
		.hero-body {
			align-items: center;
			flex-direction: column;
			justify-content: center;
		}
	</style>
</head>
<body>
<section class="hero is-fullheight">
	<nav class="nav has-shadow">
		<div class="container">
			<div class="nav-left">
				<a class="nav-item" href="https://wiecejnizlek.pl">
					<img src="{{ asset('images/wnl-logo.svg') }}" alt="Logo Więcej niż LEK">
				</a>
			</div>

			<span class="nav-toggle">
					<span></span>
					<span></span>
					<span></span>
			</span>

			<div class="nav-right nav-menu">
				<a href="https://lek.wiecejnizlek.pl/payment/account" class="nav-item">
					Zapisz się na kurs
				</a>
				<a href="@lang('common.course-website-link')" class="nav-item">
					@lang('payment.back-to-website')
				</a>
			</div>
		</div>
	</nav>
	<div class="hero-body">
		<p class="title is-1">Pracujemy nad nową wersją demo.</p>
		<p class="subtitle is-3">Zapraszamy w marcu 2019!</p>
		<p style="margin-top: 40px;">
			<img src="https://s3.eu-central-1.amazonaws.com/wnl-platform-storage/giphs/app_unavailable.gif" alt="z/w">
		</p>
	</div>
</section>
<script src="{{ mix('js/guest.js') }}"></script>
</body>
</html>
