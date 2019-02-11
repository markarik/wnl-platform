const isEditable = (element) => {
	return element.tagName === 'INPUT' ||
		element.tagName === 'SELECT' ||
		element.tagName === 'TEXTAREA' ||
		element.isContentEditable;
};

export default {
	install(Vue, {store}) {
		const module = 'activateWithShortcutKey';

		Vue.prototype.$registerFixName = (options) => {
			store.dispatch(`${module}/register`, options);
		};

		Vue.prototype.$deregisterFixName = (uid) => {
			store.dispatch(`${module}/deregister`, uid);
		};

		Vue.prototype.$setActiveInstanceFixName = (uid) => {
			store.dispatch(`${module}/setActiveInstance`, uid);
		};

		Vue.prototype.$resetActiveInstanceFixName = () => {
			store.dispatch(`${module}/resetActiveInstance`);
		};

		document.addEventListener('keydown', (event) => {
			if (isEditable(event.target)) {
				return;
			}

			switch (event.key) {
			case 't':
				store.dispatch(`${module}/focusActiveInstance`);
				break;
			case ']':
				store.dispatch(`${module}/setNextInstanceAsActive`);
				break;
			case '[':
				store.dispatch(`${module}/setPreviousInstanceAsActive`);
				break;
			}
		});
	}
};
