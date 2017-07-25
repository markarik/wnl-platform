<script>
	export default {
		functional: true,
		render: (createElement, {props}) => {
			return createElement(context.props.notificationComponent, {
				props: {
					message: props.message,
					channel: props.channel,
					action: 'odpowiedział/-a na',
					icon: 'fa-reply',
					routeContext: {
						name: 'screens',
						params: {
							courseId: props.message.content.courseId,
							lessonId: props.message.context.lessonId,
							screenId: props.message.context.screenId,
						}
					},
				},
			})
		},
		props: {
			message: {
				required: true,
				type: Object,
			},
			channel: {
				required: true,
				type: String
			},
			notificationComponent: {
				required: true,
				type: Object
			}
		}
	}
</script>
