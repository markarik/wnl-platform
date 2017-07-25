<script>
	import {baseProps} from './event'

	export default {
		functional: true,
		render: (createElement, {props}) => {
			return createElement(props.notificationComponent, {
				props: {
					message: props.message,
					channel: props.channel,
					icon: 'fa-thumbs-o-up',
					routeContext: props.message.referer,
				},
			})
		},
		props: baseProps
	}
</script>
