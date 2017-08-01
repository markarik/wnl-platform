<script>
	import {baseProps, getContextRoute, mapMessageToRoute} from './event'

	export default {
		functional: true,
		render: (createElement, {props: {message, notificationComponent}, data: {on}}) => {
			const contextRoute = () => mapMessageToRoute(message, {
				[message.objects.type]: message.objects.id,
				[message.subject.type]: message.subject.reaction_type
			})

			return createElement(notificationComponent, {
				props: {
					message,
					channel: message.channel,
					icon: 'fa-thumbs-o-up',
					routeContext: getContextRoute(contextRoute, message.referer),
				},
				on,
			})
		},
		props: baseProps
	}
</script>
