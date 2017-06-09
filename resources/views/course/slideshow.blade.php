<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link href="{{ mix('/css/slideshow.css') }}" rel="stylesheet">
	<link href="{{ mix('css/imageviewer.css') }}" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" async></script>
	<script src="{{ mix('/js/imageviewer.js') }}"></script>

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
			{!! $slides !!}
		</div>
		{{--<img src="http://www.cyfronika.com.pl/art58/trafo3faz_2.jpg"
			 data-high-res-src="https://www.lucidchart.com/publicSegments/view/23e3f21f-c803-4d3d-9a64-e9cacb371710/image.jpeg"
			 class="chart">--}}
	</div>
</div>

<script src="{{ mix('/js/slideshow.js') }}"></script>

<script type="text/javascript">
	$(function () {
		var viewer = ImageViewer();
		$('.chart').click(function () {
			var imgSrc              = this.src,
				highResolutionImage = $(this).data('high-res-src');

			viewer.show(imgSrc, highResolutionImage);
		});
	});
</script>


</body>
</html>
