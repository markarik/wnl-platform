export const getContextRoute = (routeContext, fallback) => {
	let context = fallback

	try {
		context = routeContext()
	} catch (e) {
		$wnl.logger.error(e)
	}

	return context
}

export const baseProps = {
	message: {
		required: true,
		type: Object,
	},
	notificationComponent: {
		required: true,
		type: Object
	}
}

export const mapMessageToRoute = (message, query) => {
	return {
		name: message.context.name,
		params: message.context.params,
		query,
	}
}
