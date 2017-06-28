<template lang="html">
	<div class="wnl-newsfeed-notification box" :class="">
		<small class="time">{{ formattedTime }}</small>
		<component :is="componentName" :event="event">
		</component>
		<a :href="event.referer" target="_blank">zabierz mnie tam</a>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-newsfeed-notification

		.time
			color: $color-inactive-gray

		.wnl-avatar-small
			display: inline-flex
</style>

<script>
	import QnaAnswerPosted from '../newsfeed/events/QnaAnswerPosted'
	import QnaQuestionPosted from '../newsfeed/events/QnaQuestionPosted'
	import CommentPosted from '../newsfeed/events/CommentPosted'
	import ReactionAdded from '../newsfeed/events/ReactionAdded'
	import {timeFromS} from 'js/utils/time'

	export default {
		name: 'wnl-newsfeed-event',
		props: ['event'],
		computed: {
			componentName() {
				return `wnl-event-${this.event.event}`
			},
			formattedTime () {
				return timeFromS(this.event.timestamp)
			}
		},
		components: {
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-qna-question-posted': QnaQuestionPosted,
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-reaction-added': ReactionAdded
		}
	}
</script>
