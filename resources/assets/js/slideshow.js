import Postmate from 'postmate'

const Reveal = require('../../vendor/reveal/reveal.js')
const handshake = new Postmate.Model({
	goToSlide: (slideNumber) => {
		Reveal.slide(slideNumber)
	}
})

handshake.then(parent => {
	parent.emit('loaded', true)
});

Reveal.initialize({
	embedded: true,
	slideNumber: true,
	overview: false,
	transition: 'none',
	postMessage: true,
	postMessageEvents: true
})
