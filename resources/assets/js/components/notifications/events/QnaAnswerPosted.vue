<script>
	import {getContextRoute, baseProps} from './event'

	export default {
		functional: true,
		render: (createElement, {props: {message, notificationComponent}}) => {
			const contextRoute = () => ({
				name: message.context.name,
				params: message.context.params,
				query: {
					'qna_question': message.object.id
				}
			})

			return createElement(notificationComponent, {
				props: {
					message,
					channel: message.channel,
					action: 'odpowiedział/-a na',
					icon: 'fa-reply',
					routeContext: getContextRoute(contextRoute, message.referer),
				},
			})
		},
		props: baseProps
	}
</script>
