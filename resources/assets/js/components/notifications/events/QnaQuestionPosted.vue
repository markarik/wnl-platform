<script>
	import {getContextRoute, baseProps} from './event'

	export default {
		functional: true,
		render: (createElement, {props: {message, notificationComponent}}) => {
			const contextRoute = () => ({
				name: message.context.name,
				params: message.context.params
			})

			return createElement(notificationComponent, {
				props: {
					message,
					channel: message.channel,
					action: 'zadał/-a pytanie',
					icon: 'fa-question-circle-o',
					routeContext: getContextRoute(contextRoute, message.referer),
				},
			})
		},
		props: baseProps
	}
</script>
