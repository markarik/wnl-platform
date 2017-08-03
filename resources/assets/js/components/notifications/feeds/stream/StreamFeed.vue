<template>
	<div class="stream-feed">
		<div v-if="!loading">
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
				<wnl-stream-filtering :showRead="showRead" @changeFiltering="changeFiltering" @toggleShowRead="showRead = !showRead"/>
				<div class="all-seen" v-if="unseenCount > 0">
					<a v-if="!marking" class="link" @click="allSeen">
						{{$t('notifications.markAllAsRead')}}
					</a>
					<span v-else class="loader"></span>
				</div>
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
				<div class="show-more">
					<a v-if="canShowMore" class="button is-small is-outlined"
						:class="{'is-loading': fetching}"
						@click="loadMore"
					>
						{{$t('notifications.personal.showMore')}}
					</a>
					<span v-else-if="showEndInfo" class="small text-dimmed has-text-centered">
						{{$t('notifications.personal.thatsAll')}} <wnl-emoji name="+1"/>
					</span>
				</div>
			</div>
		</div>
		<wnl-text-loader v-else/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.stream-feed
		margin-top: $margin-base + $margin-small
		margin-bottom: $margin-huge

	.all-seen
		align-items: center
		display: flex
		justify-content: center
		margin-bottom: $margin-base

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

	.show-more
		align-items: center
		display: flex
		justify-content: center
</style>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import StreamNotification from 'js/components/notifications/feeds/stream/StreamNotification'
	import StreamFiltering from 'js/components/notifications/feeds/stream/StreamFiltering'
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
			'wnl-stream-filtering': StreamFiltering,
		},
		data() {
			return {
				limit: 25,
				filtering: 'all',
				marking: false,
				showRead: false,
				StreamNotification,
			}
		},
		computed: {
			...mapGetters('notifications', {
				channel: 'streamChannel',
			}),
			...mapGetters('notifications', [
				'filterSlides',
				'filterQna',
				'filterQuiz',
				'getUnseen',
				'loading',
			]),
			loading() {
				return this.totalNotifications === 0 && this.fetching
			},
			filtered() {
				let filtered = this.notifications

				if (this.filtering !== 'all') {
					filtered = this[`filter${_.upperFirst(this.filtering)}`](this.channel)
				}

				if (!this.showRead) {
					filtered = _.filter(filtered, (notification) => !notification.read_at)
				}

				return filtered
			},
			unseenCount() {
				return _.size(this.getUnseen(this.channel))
			},
			zeroStateImage() {
				return getImageUrl('notifications-zero.png')
			},
		},
		methods: {
			...mapActions('notifications', ['markAllAsSeen']),
			changeFiltering(filtering) {
				this.filtering = filtering
			},
			allSeen() {
				this.marking = true

				this.markAllAsSeen(this.channel)
					.then(() => this.marking = false)
			},
		},
	}
</script>
