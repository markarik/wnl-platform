<template>
	<div class="stream-feed">
		<p class="title is-4">Aktualności</p>

		<div class="zero-state" v-if="isEmpty">
			<img class="zero-state-image"
				:alt="$t('notifications.personal.zeroStateImage')"
				:src="zeroStateImage"
				:title="$t('notifications.personal.zeroStateImage')">
			<p class="zero-state-text">
				{{$t('notifications.personal.zeroState')}}
			</p>
		</div>
		<div v-else>
			<component :is="getEventComponent(message)"
				:message="message"
				:key="id"
				:notificationComponent="StreamNotification"
				v-for="(message, id) in notifications"
				v-if="hasComponentForEvent(message)"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.stream-feed
		margin-top: $margin-big

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
		},
		data() {
			return {
				StreamNotification,
			}
		},
		computed: {
			...mapGetters('notifications', {
				channel: 'moderatorsChannel',
			}),
			zeroStateImage() {
				return getImageUrl('notifications-zero.png')
			},
		},
	}
</script>
