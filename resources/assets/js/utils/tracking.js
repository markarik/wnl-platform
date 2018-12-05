export function gaEvent(category, action, label) {
	console.log('ga', category, action, label)
	if (typeof ga === 'function') {
		ga('send', 'event', category, action, label);
	}
}

export function gaPageView() {
	if (typeof ga === 'function') {
		ga('send', 'pageview');
	}
}