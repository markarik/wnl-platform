<script>
import { getContextRoute, baseProps, mapMessageToRoute } from './event';

export default {
	functional: true,
	props: baseProps,
	render: (createElement, { props: { message, notificationComponent }, data: { on } }) => {
		const contextRoute = () => mapMessageToRoute(message, {
			'qna_question': message.objects.id
		});
		const { objects, ...messageWithoutObject } = message;

		return createElement(notificationComponent, {
			props: {
				message: messageWithoutObject,
				channel: message.channel,
				icon: 'fa-reply',
				routeContext: getContextRoute(contextRoute, message.referer),
			},
			on,
		});
	}
};
</script>
