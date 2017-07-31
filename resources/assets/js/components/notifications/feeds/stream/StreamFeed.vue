<template>
	<div class="stream-feed">
		<div class="zero-state" v-if="isEmpty">
			<img class="zero-state-image"
				:alt="$t('notifications.personal.zeroStateImage')"
				:src="zeroStateImage"
				:title="$t('notifications.personal.zeroStateImage')">
			<p class="zero-state-text">
				{{$t('notifications.stream.zeroState')}}
			</p>
		</div>
		<div v-else>
			<wnl-stream-sorting @changeSorting="changeSorting"/>
			<div class="stream-notifications">
				<div class="stream-line"></div>
				<component :is="getEventComponent(message)"
					:message="message"
					:key="id"
					:notificationComponent="StreamNotification"
					v-for="(message, id) in filtered"
					v-if="hasComponentForEvent(message)"
				/>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.stream-feed
		margin-top: $margin-base + $margin-small
		margin-bottom: $margin-huge

	.stream-notifications
		position: relative

		.stream-line
			border-left: 1px solid $color-light-gray
			left: 30px
			height: 100%
			position: absolute
			top: 0
			z-index: -1

	.zero-state
		align-items: center
		display: flex
		flex-direction: column
		justify-content: center
		height: 100%
		padding: $margin-big
		width: 100%

		.zero-state-image
			min-width: 150px
			width: 25%

		.zero-state-text
			color: $color-gray-dimmed
			font-size: $font-size-minus-1
			margin-top: $margin-big
			text-align: center
</style>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import StreamNotification from 'js/components/notifications/feeds/stream/StreamNotification'
	import StreamSorting from 'js/components/notifications/feeds/stream/StreamSorting'
	import { CommentPosted, QnaAnswerPosted, QnaQuestionPosted } from 'js/components/notifications/events'
	import { feed } from 'js/components/notifications/feed'
	import { getImageUrl } from 'js/utils/env'

	export default {
		name: 'StreamFeed',
		mixins: [feed],
		components: {
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-qna-question-posted': QnaQuestionPosted,
			'wnl-stream-sorting': StreamSorting,
		},
		data() {
			return {
				StreamNotification,
				sorting: 'all',
			}
		},
		computed: {
			...mapGetters('notifications', {
				channel: 'streamChannel',
				filterSlides: 'filterSlides',
				filterQna: 'filterQna',
				filterQuiz: 'filterQuiz',
			}),
			filtered() {
				if (this.sorting === 'all') return this.notifications
				return this[`filter${_.upperFirst(this.sorting)}`](this.channel)
			},
			zeroStateImage() {
				return getImageUrl('notifications-zero.png')
			},
		},
		methods: {
			changeSorting(sorting) {
				this.sorting = sorting
			},
		},
	}
</script>
