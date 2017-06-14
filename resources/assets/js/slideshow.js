import Postmate from 'postmate'
import $ from 'jquery'
import {imageviewer} from '../../vendor/imageviewer/imageviewer.js'

imageviewer($, window, document)

$(function () {
	const Reveal    = require('../../vendor/reveal/reveal.js')
	const container = document.getElementsByClassName('reveal')[0]
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
		}
	})

	let $controls = $('.wnl-slideshow-control'),
		$chartsContainers = $('.slides').find('.iv-image-container'),
		viewers = [],
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
})
