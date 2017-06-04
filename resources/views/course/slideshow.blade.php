<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<link href="{{ mix('/css/slideshow.css') }}" rel="stylesheet">

		<style type="text/css">
			.reveal .controls button {
				outline: 0
			}

			.reveal.image-custom-background {
				background-image: url('{{$background_url}}');
				background-position: bottom;
				background-size: contain;
				background-repeat: no-repeat;
			}

			.reveal.white-custom-background {
				background: white !important;
				color: #0c1726 !important;
			}

			.reveal.dark-custom-background {
				background: #0c1726 !important;
				color: white !important;
			}

			.reveal.white-custom-background .backgrounds,
			.reveal.dark-custom-background .backgrounds {
				display: none !important
			}
		</style>
	</head>
	<body>
		<div class="theme-font-montserrat theme-color-white-blue" style="width: 100%; height: 100%;">
			<div class="reveal image-custom-background">
				<div class="slides">
					@foreach($slides as $slide)
						{!! $slide->content !!}
					@endforeach
				</div>
			</div>
		</div>
		<script src="{{ mix('/js/slideshow.js') }}"></script>
	</body>
</html>
