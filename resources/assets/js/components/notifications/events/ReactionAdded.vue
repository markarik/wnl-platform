<script>
	import {baseProps} from './event'

	export default {
		functional: true,
		render: (createElement, context) => {
			return createElement(props.notificationComponent, {
				props: {
					message: context.props.message,
					channel: context.props.channel,
					action: 'polubił/-a',
					icon: 'fa-thumbs-o-up',
					routeContext: context.props.message.referer,
				},
			})
		},
		props: baseProps
	}
</script>
