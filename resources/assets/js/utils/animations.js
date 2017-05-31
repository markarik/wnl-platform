export function scrollToTop() {
	let scrollable = document.getElementsByClassName('scrollable-main-container')[0]

	if (typeof scrollable !== 'undefined') {
		scrollable.scrollTop = 0
	}
}
