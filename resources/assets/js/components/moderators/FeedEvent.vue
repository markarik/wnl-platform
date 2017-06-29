<template lang="html">
	<div class="wnl-newsfeed-notification box" :class="{'is-read': isRead}">
		<small class="time">{{ formattedTime }}</small>
		<component :is="componentName" :event="event">
		</component>
		<a :href="event.referer" target="_blank">zabierz mnie tam</a>
		<span class="icon is-small mark-read" @click="markAsRead(event)" v-if="!isRead">
			<i class="fa fa-check"></i>
		</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-newsfeed-notification
		position: relative
		.time
			color: $color-inactive-gray
		.wnl-avatar-small
			display: inline-flex
		.mark-read
			cursor: pointer
			display: flex
			color: $color-green
			position: absolute
			right: $margin-medium
			top: $margin-medium
		&.is-read, &.is-read a
			color: $color-inactive-gray
</style>
<script>
	import QnaAnswerPosted from 'js/components/newsfeed/events/QnaAnswerPosted'
	import QnaQuestionPosted from 'js/components/newsfeed/events/QnaQuestionPosted'
	import CommentPosted from 'js/components/newsfeed/events/CommentPosted'
	import ReactionAdded from 'js/components/newsfeed/events/ReactionAdded'
	import {timeFromS} from 'js/utils/time'
	import {mapActions} from 'vuex'
	export default {
		name: 'wnl-newsfeed-event',
		props: ['event'],
		computed: {
			componentName() {
				return `wnl-event-${this.event.event}`
			},
			formattedTime () {
				return timeFromS(this.event.timestamp)
			},
			isRead () {
				return !!this.event.read_at
			}
		},
		methods: {
			...mapActions('notifications', ['markAsRead']),
		},
		components: {
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-qna-question-posted': QnaQuestionPosted,
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-reaction-added': ReactionAdded
		}
	}
</script>
