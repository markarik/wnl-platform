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
	channel: {
		required: true,
		type: String
	},
	notificationComponent: {
		required: true,
		type: Object
	}
}
