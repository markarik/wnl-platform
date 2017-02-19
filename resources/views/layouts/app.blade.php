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
	<script src="https://use.fontawesome.com/e3024fa4e4.js"></script>
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
