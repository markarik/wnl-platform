import Postmate from 'postmate'

const Reveal = require('../../vendor/reveal/reveal.js')

Reveal.initialize({
	slideNumber: true,
	overview: false,
	transition: 'none',
})

const handshake = new Postmate.Model({
	height: () => document.height || document.body.offsetHeight
});

handshake.then(parent => {
	parent.emit('loaded', true);
});
