import SweetScroll from 'sweet-scroll';
import {isDebug} from 'js/utils/env';

export function scrollToTop() {
	scrollToY(0);
}

export function scrollToElement(element, distance = 150, duration = 500, scrollable = false) {
	if (element) {
		scrollToY(element.offsetTop + element.offsetParent.offsetTop - distance, duration, scrollable);
	}
}

export function scrollWithMargin(scrollTop, duration = 500) {
	scrollToY(scrollTop - 0.4 * window.innerHeight, duration);
}

export function scrollToY(scrollTop, duration = 500, scrollable = false) {
	let container = scrollable || document.getElementsByClassName('scrollable-main-container')[0];

	if (typeof container === 'undefined') return false;

	if (isDebug() && container.scrollTop === 0) {
		// I set scrollTop to 1 to allow SweetScroll to recognize the container
		// as scrollable when the dev tools are open.
		// An issue has been filed - https://github.com/tsuyoshiwada/sweet-scroll/issues/38
		container.scrollTop = 1;
	}

	scroll = new SweetScroll({
		duration,
	}, container);

	scroll.to(scrollTop);
}
