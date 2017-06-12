<!doctype html>
<html>
<head>
	<title>Slideshow</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link href="{{ mix('/css/reveal.css') }}" rel="stylesheet">
	<link href="{{ mix('/css/slideshow.css') }}" rel="stylesheet">
	<link href="{{ mix('/css/imageviewer.css') }}" rel="stylesheet">

	<style type="text/css">
		.reveal.image-custom-background {
			background-image: url('{{$background_url}}');
		}

		.chart,
		.iv-container,
		.iv-image-container {
			height: 100%;
			width: 100%;
		}

		.iv-container {
			background-color: rgba(230,230,230,0.5);
			border: 1px solid #7a7f91 !important;
		}
	</style>
</head>
	<body>
		<div class="theme-font-montserrat theme-color-white-blue" style="width: 100%; height: 100%;">
			<div class="reveal image-custom-background">
				<div class="slides">
					{!! $slides !!}
				</div>
			</div>
			<div style="background-image: url('{{ asset('/images/slide-control-arrow.svg') }}')" class="wnl-slideshow-control navigate-right enabled" aria-label="next slide"></div>
			<div style="background-image: url('{{ asset('/images/slide-control-arrow.svg') }}')" class="wnl-slideshow-control navigate-left enabled" aria-label="previous slide">
			</div>
		</div>
		<div class="slideshow-annotations">

		</div>
		<div class="slideshow-fullscreen-menu">
			<div class="menu-item annotations">
				<a class="toggle-annotations rounded-button without-image">
					<img src="{{ asset('images/comments.svg') }}" alt="Komentarze do slajdu">
				</a>
			</div>
			<div class="menu-item go-to-slide"></div>
			<div class="menu-item close-fullscreen">
				<a class="toggle-fullscreen rounded-button without-image">
					<img src="{{ asset('images/close-fullscreen.svg') }}" alt="Pełen ekran">
				</a>
			</div>
		</div>
		<script src="{{ mix('/js/slideshow.js') }}"></script>
	</body>
</html>
