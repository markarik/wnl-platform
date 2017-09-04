<script>
	import {getContextRoute, baseProps, mapMessageToRoute} from './event'

	export default {
		functional: true,
		render: (createElement, {props: {message, notificationComponent}, data: {on}}) => {
			const contextRoute = () => mapMessageToRoute(message, {
				'chatChannel': message.subject.channel,
				'messageId': message.subject.id
			})

			return createElement(notificationComponent, {
				props: {
					message,
					channel: message.channel,
					icon: 'fa-comment',
					routeContext: getContextRoute(contextRoute, message.referer),
				},
				on,
			})
		},
		props: baseProps
	}
</script>
