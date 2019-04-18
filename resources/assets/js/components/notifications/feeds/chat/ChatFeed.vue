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
				<span v-if="!connecting" class="loader"><i class="fas fa-circle-notch fa-pulse" /></span>
				<span class="icon">
					<i class="fa" :class="iconClass" />
				</span>
			</div>
			<div slot="content">
				<div class="feed-header">
					<span v-t="'notifications.chat.heading'" class="feed-heading" />
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
						<p v-t="'notifications.chat.zeroState'" class="zero-state-text" />
					</div>
					<div v-else class="feed-content">
						<wnl-conversations-list :with-search="false" />
					</div>
				</div>

				<div class="feed-footer">
					<router-link :to="{ name: 'messages' }">
						<span v-t="'notifications.chat.footer'" class="messages-dashboard-redirect" />
					</router-link>
				</div>
			</div>
		</wnl-dropdown>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.dropdown-container
		align-items: center
		display: flex
		height: $navbar-height
		justify-content: center
		width: 100%

</style>

<script>
import { mapGetters } from 'vuex';
import Dropdown from 'js/components/global/Dropdown';
import NotificationsToggle from 'js/components/notifications/feeds/personal/NotificationsToggle';
import ConversationsList from 'js/components/messages/ConversationsList';
import { getImageUrl } from 'js/utils/env';

export default {
	name: 'ChatFeed',
	components: {
		'wnl-dropdown': Dropdown,
		'wnl-notifications-toggle': NotificationsToggle,
		'wnl-conversations-list': ConversationsList,
	},
	data() {
		return {
			isActive: false,
			isEmpty: false,
			setting: 'private_chat_nofitications',
			icons: ['fa-comment-o', 'fa-comment'],
		};
	},
	computed: {
		...mapGetters(['isTouchScreen', 'getSetting']),
		...mapGetters('chatMessages', ['status', 'unreadConversations']),
		unseenCount() {
			return this.unreadConversations > 9 ? '9+' : this.unreadConversations;
		},
		connecting() {
			return this.status;
		},
		iconClass() {
			return this.isOn ? this.icons[1] : this.icons[0];
		},
		isOn() {
			return this.getSetting(this.setting);
		},
		zeroStateImage() {
			return getImageUrl('notifications-zero.png');
		},
	},
	methods: {
		toggle(isActive) {
			this.isActive = isActive;
		},
		toggleNotifications() {
			this.isOn = this.data;
		}
	}
};
</script>
