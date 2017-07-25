<script>
	import {getContextRoute, baseProps} from './event'

	export default {
		functional: true,
		render: (createElement, {props}) => {
			const contextRoute = () => ({
				name: 'screens',
				params: {
					courseId: props.message.context.courseId,
					lessonId: props.message.context.lessonId,
					screenId: props.message.context.screenId,
				}
			})

			return createElement(props.notificationComponent, {
				props: {
					message: props.message,
					channel: props.channel,
					icon: 'fa-reply',
					routeContext: getContextRoute(contextRoute, props.message.referer),
				},
			})
		},
		props: baseProps
	}
</script>
