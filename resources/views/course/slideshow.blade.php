<!doctype html>
<html>
<head>
	<title>Slideshow</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

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
		<span id="orderNumberContainer" class="order-number-container"></span>
		<a class="bookmark rounded-button">
			<img class="bookmark-image bookmark-image-add" src="{{ asset('svg/star-o.svg') }}" alt="Dodaj do zakładek">
			<img class="bookmark-image bookmark-image-remove" src="{{ asset('svg/star.svg') }}" alt="Usuń z zakładek">
			<span>zapisz</span>
		</a>
		<div class="theme-font-montserrat theme-color-white-blue" style="width: 100%; height: 100%;">
			<div class="reveal image-custom-background">
				<div class="slides">
					{!! $slides !!}
				</div>
			</div>
			<div style="background-image: url('{{ asset('svg/slide-control-arrow.svg') }}')" class="wnl-slideshow-control navigate-right enabled" aria-label="next slide"></div>
			<div style="background-image: url('{{ asset('svg/slide-control-arrow.svg') }}')" class="wnl-slideshow-control navigate-left enabled" aria-label="previous slide">
			</div>
		</div>
		<a class="toggle-annotations rounded-button without-image" style="display: none">
			<img src="{{ asset('svg/comments.svg') }}" alt="Komentarze do slajdu">
			<span class="badge annotations-count">0</span>
		</a>
		<div class="slideshow-annotations" style="display: none;">
			<div class="annotations-title">Komentarze do slajdu (<span class="annotations-count">0</span>)</div>
			<div class="annotations-to-slide"></div>
			{{-- <a class="annotations-new-comment">Skomentuj</a> --}}
		</div>
		<a class="toggle-fullscreen rounded-button without-image">
			<img class="fs-close" src="{{ asset('svg/close-fullscreen.svg') }}" alt="Zamknij pełen ekran">
			<img class="fs-open" src="{{ asset('svg/fullscreen-arrows.svg') }}" alt="Pełen ekran">
		</a>
		<div id="modifiedSlidesList" class="modified-slides-list">
			<button id="refreshButton" class="refresh-button">Odśwież prezentację</button>
		</div>
		<a id="refreshIcon" class="refresh-icon rounded-button without-image hidden">
			<img src="{{ asset('svg/refresh.svg') }}" alt="Odśwież prezentację">
			<span id="modifiedSlidesCounter" class="badge">0</span>
		</a>
		<script src="{{ mix('/js/slideshow.js') }}"></script>
	</body>
</html>
