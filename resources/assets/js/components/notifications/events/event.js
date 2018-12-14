import queryString from 'query-string';
import appendQuery from 'append-query';

export const getContextRoute = (routeContext, fallback) => {
	let context = fallback;

	try {
		context = routeContext();
	} catch (e) {
		$wnl.logger.error(e);
	}

	return context;
};

export const baseProps = {
	message: {
		required: true,
		type: Object,
	},
	notificationComponent: {
		required: true,
		type: Object
	}
};

export const mapMessageToRoute = (message, query) => {

	if (!message.context) {
		const url = message.referer.split('?')[0];

		return appendQuery(url, queryString.stringify({
			...query,
			notification: message.id,
			noScroll: true
		}));
	}

	if (message.context.dynamic) {
		return {
			dynamic: {
				resource: message.context.dynamic.resource,
				value: message.context.dynamic.value,
			},
			route: {
				...message.context.route
			},
			query: {
				...query,
				notification: message.id,
				noScroll: true
			}
		};
	}

	if (!message.context.name) {

		const url = message.referer.split('?')[0];

		return appendQuery(url, queryString.stringify({
			...query,
			notification: message.id,
			noScroll: true
		}));
	}

	return {
		name: message.context.name,
		params: message.context.params,
		query: {
			...query,
			notification: message.id,
			noScroll: true
		},
	};
};
