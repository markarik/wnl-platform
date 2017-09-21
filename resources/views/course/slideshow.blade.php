<!doctype html>
<html>
<head>
	<title>Slideshow</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
		<a class="bookmark rounded-button">
			<img class="bookmark-image bookmark-image-add" src="{{ asset('images/star-o.svg') }}" alt="Dodaj do zakładek">
			<img class="bookmark-image bookmark-image-remove" src="{{ asset('images/star.svg') }}" alt="Usuń z zakładek">
			<span>zapisz</span>
		</a>
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
		<a class="toggle-annotations rounded-button without-image" style="display: none">
			<img src="{{ asset('images/comments.svg') }}" alt="Komentarze do slajdu">
			<span class="annotations-count">0</span>
		</a>
		<div class="slideshow-annotations" style="display: none;">
			<div class="annotations-title">Komentarze do slajdu (<span class="annotations-count">0</span>)</div>
			<div class="annotations-to-slide"></div>
			{{-- <a class="annotations-new-comment">Skomentuj</a> --}}
		</div>
		<a class="toggle-fullscreen rounded-button without-image">
			<img class="fs-close" src="{{ asset('images/close-fullscreen.svg') }}" alt="Zamknij pełen ekran">
			<img class="fs-open" src="{{ asset('images/fullscreen-arrows.svg') }}" alt="Pełen ekran">
		</a>
		<script src="{{ mix('js/manifest.js') }}"></script>
		<script src="{{ mix('js/dupa.js') }}"></script>
		<script src="{{ mix('/js/slideshow.js') }}"></script>
	</body>
</html>
