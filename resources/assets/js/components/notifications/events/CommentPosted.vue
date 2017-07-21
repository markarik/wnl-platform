<template>
	<div class="event-comment-posted">
		<slot
			:action="action"
			:icon="icon"
		></slot>
	</div>
</template>

<script>
	import { baseEvent } from 'js/components/notifications/base-event'

	export default {
		name: 'CommentPosted',
		mixins: [baseEvent],
		props: {
			message: {
				required: true,
				type: Object,
			},
		},
		computed: {
			action() {
				return 'skomentował/-a'
			},
			icon() {
				return 'fa-comments-o'
			},
		},
		methods: {
			buildContext() {
				return {
					'qna_answer': {
						name: 'screens',
						params: {
							courseId: this.message.context.courseId,
							lessonId: this.message.context.lessonId,
							screenId: this.message.context.screenId,
						},
					}
				}[this.message.object.type]
			}
		}
	}
</script>
