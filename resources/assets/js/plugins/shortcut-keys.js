const isEditable = (element) => {
	return element.tagName === 'INPUT' ||
		element.tagName === 'SELECT' ||
		element.tagName === 'TEXTAREA' ||
		element.isContentEditable;
};

export default {
	install(Vue, {store}) {
		const module = 'activateWithShortcutKey';

		Vue.prototype.$shortcutKeyRegister = (options) => {
			store.dispatch(`${module}/register`, options);
		};

		Vue.prototype.$shortcutKeyDeregister = (uid) => {
			store.dispatch(`${module}/deregister`, uid);
		};

		Vue.prototype.$shortcutKeySetActiveInstance = (uid) => {
			store.dispatch(`${module}/setActiveInstance`, uid);
		};

		Vue.prototype.$shortcutKeyResetActiveInstance = () => {
			store.dispatch(`${module}/resetActiveInstance`);
		};

		Vue.prototype.$shortcutKeyIsEditable = isEditable;

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
