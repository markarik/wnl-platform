export function scrollToTop() {
	scrollToY(0)
}

export function scrollToElement(element, distance = 150) {
	if (typeof element !== undefined) {
		scrollToY(element.offsetTop - distance)
	}
}

export function scrollToY(scrollTop) {
	let container = document.getElementsByClassName('scrollable-main-container')[0]
	if (typeof container !== undefined) {
		container.scrollTop = scrollTop
	}
}
