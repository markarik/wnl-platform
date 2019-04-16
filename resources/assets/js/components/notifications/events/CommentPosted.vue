<script>
import { getContextRoute, baseProps, mapMessageToRoute } from './event';

export default {
	functional: true,
	props: baseProps,
	render: (createElement, { props: { message, notificationComponent }, data: { on } }) => {
		const contextRoute = () => mapMessageToRoute(message, {
			[message.objects.type]: message.objects.id,
			[message.subject.type]: message.subject.id
		});

		return createElement(notificationComponent, {
			props: {
				message,
				channel: message.channel,
				icon: 'fa-comments-o',
				routeContext: getContextRoute(contextRoute, message.referer),
			},
			on,
		});
	}
};
</script>
