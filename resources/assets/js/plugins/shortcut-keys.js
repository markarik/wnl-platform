export default (Vue, {store}) => {
	const module = 'activateWithShortcutKey';

	document.addEventListener('keydown', (event) => {
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
