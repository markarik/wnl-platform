<script>
	import {getContextRoute, baseProps} from './event'

	export default {
		functional: true,
		render: (createElement, {props}) => {
			return createElement(props.notificationComponent, {
				props: {
					message: props.message,
					channel: props.channel,
					action: 'odpowiedział/-a na',
					icon: 'fa-reply',
					routeContext: getContextRoute({
						name: 'screens',
						params: {
							courseId: props.message.content.courseId,
							lessonId: props.message.context.lessonId,
							screenId: props.message.context.screenId,
						}
					}, message.referer),
				},
			})
		},
		props: baseProps
	}
</script>
