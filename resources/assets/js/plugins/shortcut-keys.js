const isEditable = (element) => {
	return element.tagName === 'INPUT' ||
		element.tagName === 'SELECT' ||
		element.tagName === 'TEXTAREA' ||
		element.isContentEditable;
};

export default (Vue, {store}) => {
	const module = 'activateWithShortcutKey';

	document.addEventListener('keydown', (event) => {
		if (isEditable(event.target)) {
			return;
		}

		switch (event.key) {
		case 't':
			store.dispatch(`${module}/setFirstInstanceAsActive`);
			break;
		case ']':
			store.dispatch(`${module}/setNextInstanceAsActive`);
			break;
		case '[':
			store.dispatch(`${module}/setPreviousInstanceAsActive`);
			break;
		}
	});
};
