import Postmate from 'postmate'

const Reveal = require('../../vendor/reveal/reveal.js')

Reveal.initialize({
	slideNumber: true,
	overview: false,
	transition: 'none',
})

const handshake = new Postmate.Model({
	goToSlide: (slideNumber) => {
		console.log('slideNumber ' + slideNumber)
		Reveal.slide(slideNumber)
	}
});

handshake.then(parent => {
	parent.emit('loaded', true);
});
