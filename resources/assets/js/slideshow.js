import $ from 'jquery'
import {template} from 'lodash'
import Postmate from 'postmate-fork'
import Reveal from '../../vendor/reveal/reveal'
import {imageviewer} from '../../vendor/imageviewer/imageviewer'

import {timeFromS} from 'js/utils/time'

imageviewer($, window, document)

const container             = document.getElementsByClassName('reveal')[0]
const $controls             = $('.wnl-slideshow-control')
const $chartsContainers     = $('.slides').find('.iv-image-container')
const $slideshowAnnotations = $('.slideshow-annotations')
const $slideAnnotations     = $slideshowAnnotations.find('.annotations-to-slide')
const $annotationsCounters  = $('.annotations-count')
const $toggleAnnotations    = $('.toggle-annotations')
const $toggleFullscreen     = $('.toggle-fullscreen')
const bookmarkElement       = document.querySelector('.bookmark')
const bookmarkImageAdd      = bookmarkElement.querySelector('.bookmark-image-add')
const bookmarkImageRemove   = bookmarkElement.querySelector('.bookmark-image-remove')

let isSavingBookmark = false
let bookmarkedSlideNumbers = [];

const handshake = new Postmate.Model({
	changeBackground: (background) => {
		let containerClass = container.className,
			backgroundClassExp = /[a-z]+\-custom\-background/g

		if (backgroundClassExp.test(containerClass)) {
			container.className = containerClass.replace(backgroundClassExp, `${background}-custom-background`)
		} else {
			container.className += ` ${background}-custom-background`
		}
	},
	goToSlide: (slideNumber) => {
		Reveal.slide(slideNumber)
	},
	toggleFullscreen: (isFullscreen) => {
		if (isFullscreen) {
			$toggleAnnotations.show()
			$toggleFullscreen.addClass('is-fullscreen')
		} else {
			$toggleAnnotations.hide()
			$slideshowAnnotations.hide()
			$toggleFullscreen.removeClass('is-fullscreen')
		}
	},
	updateAnnotations: (annotationsData) => {
		let annotationsLength = annotationsData.length

		$slideAnnotations.find('.slide-annotations-container').hide()
		$annotationsCounters.text(annotationsLength)

		if (annotationsLength === 0) {
			$annotationsCounters.removeClass('has-some')
			return false
		}

		let slideId = annotationsData[0].commentable_id,
			elementId = `slide-annotations-${slideId}`,
			$annotationsContainer = $slideAnnotations.find(`#${elementId}`)

		$annotationsCounters.addClass('has-some')

		if (!$annotationsContainer.length) {
			$slideAnnotations.append(`
				<div id="${ elementId }" class="slide-annotations-container" style="display: none;">
				</div>`
			)
			$annotationsContainer = $slideAnnotations.find(`#${elementId}`)
		} else if ($annotationsContainer.find('annotation').length !== annotationsLength) {
			$annotationsContainer.empty()
		} else {
			return false
		}

		$annotationsContainer.append(createAnnotations(annotationsData))
		$annotationsContainer.show()
	},
	setupBookmarks(currentBookmarkedSlideNumbers) {
		bookmarkedSlideNumbers = currentBookmarkedSlideNumbers;
		setBookmarkedState(Reveal.getState().indexh)
		isSavingBookmark = false
	},
	setBookmarkedState(data) {
		const bookmarkedClassname = 'is-bookmarked';

		bookmarkedSlideNumbers.push(data.slideIndex)

		if (data.hasReacted) {
			bookmarkElement.classList.add(bookmarkedClassname);
		} else {
			bookmarkElement.classList.remove(bookmarkedClassname);
		}

		isSavingBookmark = false
	}
})

let parent = {},
	viewers = [],
	fullScreenViewer = {}

handshake.then(parentWindow => {
	parent = parentWindow
	parent.emit('loaded', true)
	setMenuListeners(parent)
	setBookmarks(parent)
}).catch(exception => {
	console.error(exception)

	// TODO: Bart, help me do it better... :/

	handshake.then(parentWindow => {
		parent = parentWindow
		parent.emit('loaded', true)
		setMenuListeners(parent)
	}).catch(exception => {
		console.error(exception)
	})
})

Reveal.initialize({
	backgroundTransition: 'none',
	center: false,
	controls: false,
	embedded: true,
	slideNumber: true,
	overview: false,
	transition: 'none',
	postMessage: true,
	postMessageEvents: true,
	progress: true,
})

Reveal.addEventListener('slidechanged', (event) => {
	let $chartContainer = $(event.currentSlide).find('.iv-image-container')

	if ($chartContainer.length > 0) {
		let index = $.inArray($chartContainer[0], $chartsContainers)
		if (index > -1) {
			viewers[index].refresh()
		}
	}

	setBookmarkedState(event.indexh);
})

if ($controls.length > 0) {
	$.each($controls, (index, element) => {
		$(element).on('click', handleControlClick)
	})
}

$(() => {
	fullScreenViewer = ImageViewer($('#iv-container'), {snapViewPersist: false})

	$.each($chartsContainers, (index, container) => {
		let $container = $(container),
			$element = $container.find('.chart'),
			lofi = $element.attr('src'),
			hifi = $element.attr('data-high-res-src')

		viewers[index] = ImageViewer($element)
		$container.find('.iv-image-fullscreen').click({lofi, hifi}, (e) => {
			fullScreenViewer.show(e.data.lofi, e.data.hifi)
		})
	})
})

function animateControl(event) {
	let target = event.target

	if (typeof target !== undefined) {
		target.classList.remove('clicked')
		void target.offsetWidth
		target.classList.add('clicked')
	}
}

function handleControlClick(event) {
	animateControl(event)
	$.each(viewers, (index, viewer) => viewer.refresh())
}

function createAnnotations(annotations) {
	let annotationsHtml = ''

	annotations.forEach(annotation => {
		annotationsHtml += `
			<div class="annotation">
				<div class="annotation-meta">
					<span class="author">${ annotation.profiles[0].full_name }</span> ·
					<span class="time">${ timeFromS(annotation.created_at) }</span>
				</div>
				<div class="annotation-content">${ annotation.text }</div>
			</div>`
	})

	return annotationsHtml
}

function toggleAnnotations() {
	$slideshowAnnotations.toggle()
}

function setMenuListeners(parent) {
	$toggleFullscreen.on('click', () => {
		const bodyElement = document.getElementsByTagName('body')[0];

		if (!document.mozFullScreen && typeof bodyElement.mozRequestFullScreen === 'function') {
			bodyElement.mozRequestFullScreen();
		}

		emitToggleFullscreen();
	});
	$toggleAnnotations.on('click', toggleAnnotations)
}

function setBookmarks(parent) {
	$('.bookmark').click(function (event) {
		if (isSavingBookmark) return

		isSavingBookmark = true;
		parent.emit('bookmark', {
			index: Reveal.getState().indexh,
			isBookmarked: this.classList.contains('is-bookmarked')
		});
	});
}

function setBookmarkedState(currentSlideNumber) {
	console.log('currentSlideNumber', currentSlideNumber)
	console.log('bookmarkedSlideNumbers', bookmarkedSlideNumbers)

	const bookmarkedClassname = 'is-bookmarked';

	if (bookmarkedSlideNumbers.indexOf(currentSlideNumber) > -1 ) {
		bookmarkElement.classList.add(bookmarkedClassname);
	} else {
		bookmarkElement.classList.remove(bookmarkedClassname);
	}
}

function emitToggleFullscreen() {
	parent.emit('toggle-fullscreen', true)
}
