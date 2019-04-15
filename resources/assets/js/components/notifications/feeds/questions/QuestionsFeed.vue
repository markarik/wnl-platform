<template>
	<div class="questions-feed">
		<div v-if="!loading">
			<div class="stream-notifications">
				<div class="stream-line"></div>
				<component
					:is="getEventComponent(message)"
					:message="message"
					:key="message.id"
					:notification-component="QuestionsNotification"
					v-for="message in filtered"
				/>
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
		<wnl-text-loader class="margin vertical" v-else />
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.questions-feed
		margin-bottom: $margin-huge

	.all-seen
		align-items: center
		display: flex
		font-size: $font-size-minus-2
		justify-content: flex-end
		margin: $margin-base 0
		text-transform: uppercase

	.questions-notifications
		position: relative

		.questions-line
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
		+flex-center()
		margin-top: $margin-base
</style>

<script>
import { mapGetters } from 'vuex';

import QuestionsNotification from 'js/components/notifications/feeds/questions/QuestionsNotification';
import { CommentPosted } from 'js/components/notifications/events';
import { feed } from 'js/components/notifications/feed';

export default {
	name: 'QuestionsFeed',
	mixins: [feed],
	components: {
		'wnl-event-comment-posted': CommentPosted,
	},
	data() {
		return {
			limit: 100,
			showRead: false,
			QuestionsNotification,
		};
	},
	computed: {
		...mapGetters('notifications', {
			channel: 'streamChannel',
		}),
		...mapGetters('notifications', [
			'filterQuiz',
			'getUnread',
			'getRead',
			'loading',
		]),
		loading() {
			return this.totalNotifications === 0 && this.fetching;
		},
		filtered() {
			return Object.values(this.filterQuiz(this.channel)).filter(message => this.hasComponentForEvent(message));
		},
	},
};
</script>
