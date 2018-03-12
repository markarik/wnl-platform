<template lang="html">
	<div class="dropdown-container">
	 	<wnl-dropdown :options="{isWide: true}" @toggled="toggle" ref="dropdown">
			<div slot="activator" class="notifications-toggle"
				:class="{ 'is-active': isActive, 'is-off': !isOn, 'is-desktop': !isTouchScreen }">
				<div v-if="isOn && !!unseenCount" class="counter">{{ unseenCount }}</div>
				<span v-if="!connecting" class="loader"><i class="fas fa-circle-notch fa-pulse"></i></span>
				<span class="icon">
					<i class="fa" :class="iconClass"></i>
				</span>
			</div>
			<div slot="content">
				<div class="feed-header">
					<span class="feed-heading"v-t="'notifications.chat.heading'"/>
					<wnl-chat-notifications-toggle/>
				</div>

				<div class="feed-body">
					<div class="zero-state" v-if="isEmpty">
						<img class="zero-state-image"
							:alt="$t('notifications.personal.zeroStateImage')"
							:src="zeroStateImage"
							:title="$t('notifications.personal.zeroStateImage')">
						<p class="zero-state-text" v-t='"notifications.chat.zeroState"'/>
					</div>
					<div class="feed-content" v-else>
						<wnl-conversations-list :withSearch="false"/>
					</div>
				</div>

				<div class="feed-footer">
					<router-link :to="{ name: 'messages' }">
			   			<span class="messages-dashboard-redirect" v-t="'notifications.chat.footer'"/>
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
	import { mapActions, mapGetters } from 'vuex'
	import Dropdown from 'js/components/global/Dropdown'
	import ChatNotificationsToggle from 'js/components/notifications/feeds/chat/ChatNotificationsToggle'
	import ConversationsList from 'js/components/messages/ConversationsList'

	export default {
		name: 'ChatFeed',
		components: {
			'wnl-dropdown': Dropdown,
			'wnl-chat-notifications-toggle': ChatNotificationsToggle,
			'wnl-conversations-list': ConversationsList,
		},
		data() {
			return {
				isActive: false,
				isEmpty: false,
			}
		},
		computed: {
			...mapGetters(['isTouchScreen', 'getSetting']),
			...mapGetters('chatMessages', ['status', 'getUnseenRooms']),
			unseenCount() {
				return this.getUnseenRooms
			},
			connecting() {
				return this.status
			},
			iconClass() {
				return this.isOn ? 'fa-comment' : 'fa-comment-o'
			},
			isOn() {
				return this.getSetting(ChatNotificationsToggle.SETTING)
			},
			zeroStateImage() {
				return getImageUrl('notifications-zero.png')
			},
		},
		methods: {
			toggle(isActive) {
				this.isActive = isActive
			},
			toggleNotifications(data) {
				this.isOn = this.data
			}
		}
	}
</script>
