<script>
import { baseProps, getContextRoute, mapMessageToRoute } from './event';

export default {
	functional: true,
	render: (createElement, { props: { message, notificationComponent }, data: { on } }) => {
		const query = {
			[message.objects.type]: message.objects.id,
			[message.subject.type]: message.subject.reaction_type
		};

		if (message.commentable) {
			query[message.commentable.type] = message.commentable.id;
		}
		const contextRoute = () => mapMessageToRoute(message, query);

		return createElement(notificationComponent, {
			props: {
				message,
				channel: message.channel,
				icon: 'fa-thumbs-o-up',
				routeContext: getContextRoute(contextRoute, message.referer),
			},
			on,
		});
	},
	props: baseProps
};
</script>
