<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link href="{{ asset('/css/reveal.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/reveal-theme.css') }}" rel="stylesheet">
</head>
<body>
<div class="theme-font-montserrat theme-color-white-blue" style="width: 100%; height: 100%;">
	<div class="reveal">
		<div class="slides">
			@foreach($slides as $slide)
				{!! $slide->content !!}
			@endforeach
		</div>
	</div>
</div>
<script src="{{asset('/js/reveal.js')}}"></script>
<script> Reveal.initialize(); </script>
</body>
</html>