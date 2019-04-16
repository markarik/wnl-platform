<template>
	<div class="stream-feed">
		<div v-if="!loading">
			<div>
				<wnl-stream-filtering
					:show-read="showRead"
					@changeFiltering="changeFiltering"
					@toggleShowRead="toggleShowRead"
				/>
				<div class="stream-notifications">
					<div class="stream-line" />
					<component
						:is="getEventComponent(message)"
						v-for="message in filtered"
						:key="message.id"
						:message="message"
						:notification-component="StreamNotification"
					/>
				</div>
				<div v-if="!showRead && unreadCount > 0" class="all-seen">
					<a
						v-if="!marking"
						class="link"
						@click="allRead"
					>
						{{$t('notifications.hideAll')}}
					</a>
					<span v-else class="loader" />
				</div>
				<div class="show-more">
					<a
						v-if="canShowMore"
						class="button is-small is-outlined"
						:class="{'is-loading': fetching}"
						@click="loadMore"
					>
						{{$t('notifications.personal.showMore')}}
					</a>
					<span v-else class="small text-dimmed has-text-centered">
						{{$t('notifications.personal.thatsAll')}} <wnl-emoji name="+1" />
					</span>
				</div>
			</div>
		</div>
		<wnl-text-loader v-else />
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
		font-size: $font-size-minus-2
		justify-content: flex-end
		margin-bottom: $margin-base
		text-transform: uppercase

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
			color: $color-gray
			font-size: $font-size-minus-1
			margin-top: $margin-big
			text-align: center

	.show-more
		align-items: center
		display: flex
		justify-content: center
</style>

<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';

import StreamNotification from 'js/components/notifications/feeds/stream/StreamNotification';
import StreamFiltering from 'js/components/notifications/feeds/stream/StreamFiltering';
import { CommentPosted, QnaAnswerPosted, QnaQuestionPosted } from 'js/components/notifications/events';
import { feed } from 'js/components/notifications/feed';
import { getImageUrl } from 'js/utils/env';

export default {
	name: 'StreamFeed',
	components: {
		'wnl-event-comment-posted': CommentPosted,
		'wnl-event-qna-answer-posted': QnaAnswerPosted,
		'wnl-event-qna-question-posted': QnaQuestionPosted,
		'wnl-stream-filtering': StreamFiltering,
	},
	mixins: [feed],
	data() {
		return {
			limit: 100,
			filtering: 'all',
			marking: false,
			showRead: false,
			StreamNotification,
		};
	},
	computed: {
		...mapGetters('notifications', {
			channel: 'streamChannel',
		}),
		...mapGetters('notifications', [
			'filterSlides',
			'filterQna',
			'filterQuiz',
			'getUnread',
			'getRead',
			'loading',
		]),
		loading() {
			return this.totalNotifications === 0 && this.fetching;
		},
		filtered() {
			let filtered = this.notifications;

			if (this.filtering !== 'all') {
				filtered = this[`filter${_.upperFirst(this.filtering)}`](this.channel);
			}

			filtered = _.filter(filtered, (notification) => this.showRead ? notification.read_at : !notification.read_at);
			filtered = filtered.filter(message => this.hasComponentForEvent(message));

			return filtered;
		},
		unreadCount() {
			return _.size(this.getUnread(this.channel));
		},
		zeroStateImage() {
			return getImageUrl('notifications-zero.png');
		},
		notificationsParams() {
			return {
				unread: !this.showRead
			};
		},
	},
	methods: {
		...mapActions('notifications', ['markAllAsRead']),
		changeFiltering(filtering) {
			this.filtering = filtering;
		},
		allRead() {
			this.marking = true;

			this.markAllAsRead(this.channel)
				.then(() => this.marking = false);
		},
		toggleShowRead() {
			this.showRead = !this.showRead;

			if (this.showRead && !_.size(this.getRead(this.channel))) {
				this.loadMore();
			}
		}
	},
};
</script>
