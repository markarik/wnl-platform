import _ from 'lodash'
import Postmate from 'postmate'

const Reveal = require('../../vendor/reveal/reveal.js')
const container = document.getElementsByClassName('reveal')[0]

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

handshake.then(parent => {
	parent.emit('loaded', true)
}).catch(exception => console.log(exception))

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

document.addEventListener('DOMContentLoaded', (event) => {
	function animateControl(event) {
		let target = event.target

		if (typeof target !== undefined) {
			target.classList.remove('clicked')
			void target.offsetWidth
			target.classList.add('clicked')
		}
	}

	let controls = document.getElementsByClassName('wnl-slideshow-control')
	if (controls.length === 0) return false;

	_.each(controls, (element) => {
		element.addEventListener('click', animateControl)
		element.addEventListener('touchstart', animateControl)
	})
}, false);
