<script>
	import {baseProps} from './event'

	export default {
		functional: true,
		render: (createElement, {props: {message, notificationComponent}}) => {
			return createElement(notificationComponent, {
				props: {
					message,
					channel: message.channel,
					icon: 'fa-thumbs-o-up',
					routeContext: message.referer,
				},
			})
		},
		props: baseProps
	}
</script>
