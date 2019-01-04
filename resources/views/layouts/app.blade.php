<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta property="og:url" content="{{env('APP_URL')}}">
	<meta property="og:type" content="website">
	<meta property="og:title" content="@lang('common.app-title')">
	<meta property="og:description" content="Unikalny kurs przygotowujący do Lekarskiego Egzaminu Końcowego">
	<meta property="og:image" content="{{ asset('/images/fbogimage.jpg') }}">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@lang('common.app-title')</title>

	<link rel="icon" href="{{ url('favicon.png') }}">

	<!-- Scripts -->
	<script>
		window.Laravel = <?php echo json_encode([
			'csrfToken' => csrf_token(),
		]); ?>
	</script>
	@include('tracking')
</head>
<body data-base="{{env('APP_URL')}}">
	<div id="app" data-view="@yield('current-view')" class="full-height"></div>

	<form method="post" action="/logout" id="logout-form">
		{{ csrf_field() }}
	</form>
	@include('not-supported-browser-modal')
	@include ('footer')
</body>
</html>
