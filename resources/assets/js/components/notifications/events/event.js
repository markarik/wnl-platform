export const getContextRoute = (routeContext, fallback) => {
	try {
		return routeContext()
	} catch (e) {
		$wnl.logger.error(e)
	} finally {
		return fallback
	}
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
