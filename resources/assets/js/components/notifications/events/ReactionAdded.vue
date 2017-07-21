<template>
	<div class="event-reaction-added">
		<slot
			:action="action"
			:icon="icon"
		></slot>
	</div>
</template>

<script>
	import { baseEvent } from 'js/components/notifications/base-event'

	export default {
		name: 'ReactionAdded',
		mixins: [baseEvent],
		props: {
			message: {
				required: true,
				type: Object,
			},
		},
		computed: {
			action() {
				return 'polubił/-a'
			},
			icon() {
				return 'fa-thumbs-o-up'
			},
		},
		methods: {
			buildContext() {
				return this.message.referer
			}
		}
	}
</script>
