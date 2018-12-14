<script>
import {getContextRoute, baseProps, mapMessageToRoute} from './event';

export default {
	functional: true,
	render: (createElement, {props: {message, notificationComponent}, data: {on}}) => {
		const contextRoute = () => mapMessageToRoute(message, {
			[message.commentable.type]: message.commentable.id,
			[message.subject.type]: null
		});
		const {objects, ...messageWithoutObject} = message;

		return createElement(notificationComponent, {
			props: {
				message: messageWithoutObject,
				channel: message.channel,
				icon: 'fa-comments-o',
				routeContext: getContextRoute(contextRoute, message.referer),
			},
			on,
		});
	},
	props: baseProps
};
</script>
