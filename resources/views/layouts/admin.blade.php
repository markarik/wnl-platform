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
	<link href="{{ mix('css/emoji.css') }}" rel="stylesheet">

	<!-- Scripts -->
	<script>
		window.Laravel = <?php echo json_encode([
				'csrfToken' => csrf_token(),
		]); ?>
	</script>
	@include('tracking')
</head>
<body>
<div id="admin" data-view="@yield('current-view')" class="full-height"></div>

<form method="post" action="/logout" id="logout-form">
	{{ csrf_field() }}
</form>

<!-- Scripts -->
<script src="{{ mix('js/admin.js') }}"></script>

</body>
</html>
