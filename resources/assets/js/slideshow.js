import $ from 'jquery'
import {template} from 'lodash'
import Postmate from 'postmate'
import Reveal from '../../vendor/reveal/reveal'
import {imageviewer} from '../../vendor/imageviewer/imageviewer'

imageviewer($, window, document)

const container             = document.getElementsByClassName('reveal')[0]
const $controls             = $('.wnl-slideshow-control')
const $chartsContainers     = $('.slides').find('.iv-image-container')
const $slideshowAnnotations = $('.slideshow-annotations')

const viewer    = ImageViewer()
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
	updateAnnotations: (annotationsData) => {
		$slideshowAnnotations.find('.slide-annotations-container').hide()

		if (annotationsData.length > 0) {
			let slideId = annotationsData[0].commentable_id,
				elementId = `slide-annotations-${slideId}`,
				$annotationsContainer = $slideshowAnnotations.find(`#${elementId}`)

			if (!$annotationsContainer.length) {
				$slideshowAnnotations.append(`
					<div id="${ elementId }" class="slide-annotations-container" style="display: none;">
					</div>`
				)
				$annotationsContainer = $slideshowAnnotations.find(`#${elementId}`)

				$annotationsContainer.append(createAnnotations(annotationsData))
			}

			$annotationsContainer.show()
		}
	},
})

let viewers = [],
	fullScreenViewer = ImageViewer()

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
		annotationsHtml += `<div class="annotation">${ annotation.text }</div>`
	})

	return annotationsHtml
}

function toggleAnnotations() {

}

function setMenuListeners(parent) {
	let $container = $('.slideshow-fullscreen-menu'),
		$annotations = $container.find('.toggle-annotations'),
		$fullscreen = $container.find('.toggle-fullscreen')

	$fullscreen.on('click', () => {
		parent.emit('toggle-fullscreen', true)
	})

	$annotations.on('click', toggleAnnotations)
}

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
})

handshake.then(parent => {
	setMenuListeners(parent)
	parent.emit('loaded', true)
}).catch(exception => console.log(exception))

if ($controls.length > 0) {
	$.each($controls, (index, element) => {
		$(element).on('click touchstart', handleControlClick)
	})
}

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
