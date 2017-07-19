<template lang="html">
	<div class="wnl-newsfeed-event">
		<div class="unread" v-if="isUnread" @click="markAsRead({notification:event, channel})"></div>
		<component :is="componentName" :event="event">
		</component>
		<small class="time">{{ formattedTime }}</small>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-newsfeed-event
		border-bottom: $border-light-gray
		padding: $margin-small
		position: relative

		&:hover
			background: $color-background-lighter-gray
			cursor: pointer

		.time
			color: $color-inactive-gray

		.wnl-avatar-small
			display: inline-flex

		.unread
			position: absolute
			background: $color-ocean-blue
			color: $color-white
			top: 10px
			right: 10px
			width: 10px
			height: 10px
			border-radius: 100%
</style>

<script>
	import QnaAnswerPosted from './events/QnaAnswerPosted'
	import QnaQuestionPosted from './events/QnaQuestionPosted'
	import CommentPosted from './events/CommentPosted'
	import ReactionAdded from './events/ReactionAdded'
	import {mapActions, mapGetters} from 'vuex'
	import {timeFromS} from 'js/utils/time'

	export default {
		props: ['event', 'channel'],
		components: {
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-qna-question-posted': QnaQuestionPosted,
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-reaction-added': ReactionAdded
		},
		computed: {
			componentName() {
				return `wnl-event-${this.event.event}`
			},
			formattedTime () {
				return timeFromS(this.event.timestamp)
			},
			isUnread() {
				return !this.event.read_at
			}
		},
		methods: {
			...mapActions('notifications', ['markAsRead'])
		}
	}
</script>
