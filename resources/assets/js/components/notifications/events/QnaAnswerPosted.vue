<script>
import { getContextRoute, baseProps, mapMessageToRoute } from './event';

export default {
	functional: true,
	render: (createElement, { props: { message, notificationComponent }, data: { on } }) => {
		const contextRoute = () => mapMessageToRoute(message, {
			'qna_answer': message.subject.id,
			'qna_question': message.objects.id
		});

		return createElement(notificationComponent, {
			props: {
				message,
				channel: message.channel,
				icon: 'fa-reply',
				routeContext: getContextRoute(contextRoute, message.referer),
			},
			on,
		});
	},
	props: baseProps
};
</script>
