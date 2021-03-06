<template>
	<div class="dropdown-container">
		<wnl-dropdown
			ref="dropdown"
			:options="{isWide: true}"
			@toggled="toggle"
		>
			<div
				slot="activator"
				class="notifications-toggle"
				:class="{ 'is-active': isActive, 'is-off': !isOn, 'is-desktop': !isTouchScreen }"
			>
				<div v-if="isOn && !!unseenCount" class="counter">{{unseenCount}}</div>
				<span class="icon">
					<i class="fa" :class="iconClass" />
				</span>
			</div>
			<div slot="content">
				<div class="feed-header">
					<span v-t="'notifications.personal.heading'" class="feed-heading" />
					<wnl-notifications-toggle :setting="setting" :icons="icons" />
				</div>

				<div class="feed-body">
					<div v-if="isEmpty" class="zero-state">
						<img
							class="zero-state-image"
							:alt="$t('notifications.personal.zeroStateImage')"
							:src="zeroStateImage"
							:title="$t('notifications.personal.zeroStateImage')"
						>
						<p v-t="'notifications.personal.zeroState'" class="zero-state-text" />
					</div>
					<div v-else class="feed-content">
						<component
							:is="getEventComponent(message)"
							v-for="message in notificationsWithComponentForEvent"
							:key="message.id"
							:message="message"
							:notification-component="PersonalNotification"
							@goingToContext="onGoingToContext"
						/>
						<div class="show-more has-text-centered">
							<a
								v-if="canShowMore"
								v-t="'notifications.personal.showMore'"
								class="button is-small is-outlined margin vertical"
								:class="{'is-loading': fetching}"
								@click="loadMore"
							/>
							<span v-else-if="showEndInfo" class="small text-dimmed has-text-centered">
								{{$t('notifications.personal.thatsAll')}} <wnl-emoji name="+1" />
							</span>
						</div>
					</div>
				</div>

				<div v-if="unreadCount > 0" class="feed-footer">
					<a
						v-t="notifications.markAllAsRead"
						class="link"
						@click="allRead"
					/>
					<span v-if="allReadLoading" class="loader" />
				</div>
			</div>
		</wnl-dropdown>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.dropdown-container
		align-items: center
		display: flex
		height: $navbar-height
		justify-content: center
		width: 100%

</style>

<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';

import Dropdown from 'js/components/global/Dropdown';
import NotificationsToggle from 'js/components/notifications/feeds/personal/NotificationsToggle';
import PersonalNotification from 'js/components/notifications/feeds/personal/PersonalNotification';
import { CommentPosted, QnaAnswerPosted, ReactionAdded, Mentioned,
	CommentRemoved, QnaQuestionRemoved, QnaAnswerRemoved, AssignedToTask }
	from 'js/components/notifications/events';
import { feed } from 'js/components/notifications/feed';
import { getImageUrl } from 'js/utils/env';
import { USER_SETTING_NAMES } from 'js/consts/settings';

export default {
	name: 'PersonalFeed',
	components: {
		'wnl-dropdown': Dropdown,
		'wnl-event-comment-posted': CommentPosted,
		'wnl-event-comment-resolved': CommentRemoved,
		'wnl-event-comment-deleted': CommentRemoved,
		'wnl-event-qna-answer-posted': QnaAnswerPosted,
		'wnl-event-qna-answer-deleted': QnaAnswerRemoved,
		'wnl-event-qna-question-resolved': QnaQuestionRemoved,
		'wnl-event-qna-question-deleted': QnaQuestionRemoved,
		'wnl-event-reaction-added': ReactionAdded,
		'wnl-event-mentioned': Mentioned,
		'wnl-event-assigned-to-task': AssignedToTask,
		'wnl-notifications-toggle': NotificationsToggle,
	},
	mixins: [feed],
	data() {
		return {
			allReadLoading: false,
			isActive: false,
			limit: 15,
			PersonalNotification,
			setting: USER_SETTING_NAMES.NOTIFY_LIVE,
			icons: ['fa-bell-slash', 'fa-bell'],
		};
	},
	computed: {
		...mapGetters(['isTouchScreen', 'getSetting']),
		...mapGetters('notifications', {
			channel: 'userChannel',
			getUnseen: 'getUnseen',
			getUnread: 'getUnread',
		}),
		iconClass() {
			return this.isOn ? this.icons[1] : this.icons[0];
		},
		isOn() {
			return this.getSetting(this.setting);
		},
		unseenCount() {
			return _.size(_.filter(this.getUnseen(this.channel), (notification) => !notification.deleted));
		},
		unreadCount() {
			return _.size(_.filter(this.getUnread(this.channel), (notification) => !notification.deleted));
		},
		zeroStateImage() {
			return getImageUrl('notifications-zero.png');
		},
	},
	methods: {
		...mapActions('notifications', [
			'markAllAsSeen',
			'markAllAsRead',
		]),
		allRead() {
			this.allReadLoading = true;
			this.markAllAsRead(this.channel)
				.then(() => {
					this.markAllAsSeen(this.channel);
					this.allReadLoading = false;
				});
		},
		onGoingToContext() {
			if (typeof this.$refs.dropdown.toggleActive === 'function') {
				this.$refs.dropdown.toggleActive(false);
			}
		},
		toggle(isActive) {
			this.isActive = isActive;
			if (this.unseenCount > 0) {
				this.markAllAsSeen(this.channel);
			}
		},
	},
};
</script>
