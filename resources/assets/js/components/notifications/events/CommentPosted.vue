<script>
	import {getContextRoute, baseProps} from './event'

	export default {
		functional: true,
		render: (createElement, {props}) => {
			return createElement(props.notificationComponent, {
				props: {
					message: props.message,
					channel: props.channel,
					action: 'skomentował/-a',
					icon: 'fa-comments-o',
					routeContext: getContextRoute({
						'qna_answer': {
							name: 'screens',
							params: {
								courseId: props.message.context.courseId,
								lessonId: props.message.context.lessonId,
								screenId: props.message.context.screenId,
							}
						}
					}, message.referer),
				},
			})
		},
		props: baseProps
	}
</script>
