const isEditable = (element) => {
	return element.tagName === 'INPUT' ||
		element.tagName === 'SELECT' ||
		element.tagName === 'TEXTAREA' ||
		element.isContentEditable;
};

export default {
	install(Vue, { store }) {
		const module = 'activateWithShortcutKey';

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
