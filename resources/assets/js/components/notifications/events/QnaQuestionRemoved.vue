<script>
import { getContextRoute, baseProps, mapMessageToRoute } from './event';

export default {
	functional: true,
	props: baseProps,
	render: (createElement, { props: { message, notificationComponent }, data: { on } }) => {
		const contextRoute = () => mapMessageToRoute(message);

		return createElement(notificationComponent, {
			props: {
				message,
				channel: message.channel,
				icon: 'fa-question-circle-o',
				routeContext: getContextRoute(contextRoute, message.referer),
			},
			on,
		});
	}
};
</script>
