const composeItem = ({
	text,
	itemClass,
	routeName,
	routeParams = {},
	isDisabled = false,
	method = 'push',
	iconClass,
	iconTitle,
	completed = false,
	active = false,
	meta
}) => {
	const to = !isDisabled && routeName ? {
		name: routeName,
		params: routeParams
	} : {};

	return { text, itemClass, to, isDisabled, method, iconClass, iconTitle, completed, active, meta };
};

export default {
	composeItem,
};
