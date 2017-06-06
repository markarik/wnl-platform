export function scrollToTop() {
	scrollToY(0)
}

export function scrollToElement(element) {
	if (typeof element !== undefined) {
		scrollToY(element.offsetTop - 150)
	}
}

export function scrollToY(scrollTop) {
	let container = document.getElementsByClassName('scrollable-main-container')[0]
	if (typeof container !== undefined) {
		container.scrollTop = scrollTop
	}
}
