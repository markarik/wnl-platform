<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@lang('common.app-title')</title>

	<link rel="icon" href="{{ url('favicon.png') }}">

	<!-- Styles -->
	<link href="{{ mix('css/app.css') }}" rel="stylesheet">

	<!-- Scripts -->
	<script src="https://use.fontawesome.com/c95376cac6.js" async></script>
	<script src="https://use.typekit.net/hal1etr.js" async></script>
	<script>try{Typekit.load({ async: true });}catch(e){}</script>
	<script>
		window.Laravel = <?php echo json_encode([
			'csrfToken' => csrf_token(),
		]); ?>
	</script>
</head>
<body data-base="{{env('APP_URL')}}">
	<div id="app" data-view="@yield('current-view')" class="full-height"></div>

	@include ('footer')
</body>
</html>
