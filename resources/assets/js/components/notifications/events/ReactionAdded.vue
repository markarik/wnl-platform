<script>
	export default {
		functional: true,
		render: (createElement, context) => {
			return createElement(context.props.notificationComponent, {
				props: {
					message: context.props.message,
					channel: context.props.channel,
					action: 'polubił/-a',
					icon: 'fa-thumbs-o-up',
					routeContext: context.props.message.referer,
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
