<script>
	import {getContextRoute, baseProps, mapMessageToRoute} from './event'

	export default {
		functional: true,
		render: (createElement, {props: {message, notificationComponent}}) => {
			const contextRoute = () => mapMessageToRoute(message, {
				'qna_answer': message.subject.id
			})

			return createElement(notificationComponent, {
				props: {
					message,
					channel: message.channel,
					icon: 'fa-reply',
					routeContext: getContextRoute(contextRoute, message.referer),
				},
			})
		},
		props: baseProps
	}
</script>
