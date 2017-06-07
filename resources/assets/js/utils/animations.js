import SweetScroll from 'sweet-scroll'

export function scrollToTop() {
	scrollToY(0)
}

export function scrollToElement(element, distance = 150) {
	if (typeof element !== undefined) {
		scrollToY(element.offsetTop - distance)
	}
}

export function scrollWithMargin(scrollTop, duration = 500) {
	scrollToY(scrollTop - 0.4 * window.innerHeight, duration)
}

export function scrollToY(scrollTop, duration = 500) {
	let container = document.getElementsByClassName('scrollable-main-container')[0] || window,
		scroll = new SweetScroll({
			duration,
		}, container)

	scroll.to(scrollTop)
}
