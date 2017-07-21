<template>
	<div class="event-qna-answer-added">
		<slot
			:action="action"
			:icon="icon"
		></slot>
	</div>
</template>

<script>
	import { baseEvent } from 'js/components/notifications/base-event'

	export default {
		name: 'QnaAnswerPosted',
		mixins: [baseEvent],
		props: {
			message: {
				required: true,
				type: Object,
			},
		},
		computed: {
			action() {
				return 'odpowiedział/-a na'
			},
			icon() {
				return 'fa-question'
			},
		},
		methods: {
			buildContext() {
				return {
					name: 'screens',
					params: {
						courseId: this.message.content.courseId,
						lessonId: this.message.context.lessonId,
						screenId: this.message.context.screenId,
					}
				}
			}
		}
	}
</script>
