// First pageview is tracked from blade, don't duplicate this event
let isFirstPageview = true;

export function gaEvent(category, action, label) {
	if (typeof ga === 'function') {
		ga('send', 'event', category, action, label);
	}
}

export function gaPageView() {
	if (typeof ga === 'function' && !isFirstPageview) {
		ga('send', 'pageview');
	}
	isFirstPageview = false;
}
