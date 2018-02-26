<template lang="html">
	<div class="dropdown-container">
	 	<wnl-dropdown :options="{isWide: true}" @toggled="toggle" ref="dropdown">
			<div slot="activator" class="chat-notifications-toggle"
				:class="{ 'is-active': isActive, 'is-off': !isOn, 'is-desktop': !isTouchScreen }">
				<div v-if="isOn && !!unseenCount" class="counter">{{ unseenCount }}</div>
				<span class="icon">
					<i class="fa" :class="iconClass"></i>
				</span>
			</div>
			<div slot="content">
				<div class="chat-feed-header">
					<span class="chat-feed-heading"v-t="'notifications.chat.heading'"/>
					<wnl-chat-notifications-toggle/>
				</div>

				<div class="chat-feed-body">
					<div class="zero-state" v-if="isEmpty">
						<img class="zero-state-image"
							:alt="$t('notifications.personal.zeroStateImage')"
							:src="zeroStateImage"
							:title="$t('notifications.personal.zeroStateImage')">
						<p class="zero-state-text" v-t='"notifications.chat.zeroState"'/>
					</div>
					<div class="chat-feed-content" v-else>
						<wnl-conversations-list :withoutHeader="true"/>
					</div>
				</div>

				<div class="chat-feed-footer">
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
	@import 'resources/assets/sass/mixins'

	$header-height: 40px
	$footer-height: 40px
	$body-margin-top: $header-height
	$body-margin-bottom: $footer-height

	.dropdown-container
		align-items: center
		display: flex
		height: $navbar-height
		justify-content: center
		width: 100%

		.wnl-dropdown
			width: 100%

	.chat-notifications-toggle
		align-items: center
		color: $color-gray-dimmed
		cursor: pointer
		display: flex
		height: 100%
		justify-content: center
		min-height: 100%

		&.is-active
			background-color: $color-background-light-gray
			color: $color-gray

		&.is-off
			color: $color-inactive-gray

			&.is-active
				color: $color-white

		.icon
			margin: 0 $margin-tiny

	.counter
		align-items: center
		background: $color-ocean-blue
		border-radius: $border-radius-full
		color: $color-white
		display: flex
		font-size: $font-size-minus-3
		font-weight: $font-weight-black
		justify-content: center
		height: 1.7em
		position: absolute
		left: ($navbar-height / 2.1)
		top: $margin-medium
		width: 1.7em

	.chat-feed
		position: relative

	.chat-feed-header,
	.chat-feed-footer
		align-items: center
		background: $color-white
		display: flex
		position: absolute
		width: 100%
		z-index: $z-index-overlay

	.chat-feed-header
		border-radius: $border-radius-small $border-radius-small 0 0
		border-bottom: $border-light-gray
		height: $header-height
		justify-content: space-between
		padding: $margin-small $margin-medium
		top: 0

		.chat-feed-heading
			font-size: $font-size-minus-2
			font-weight: $font-weight-bold
			text-transform: uppercase

	.chat-feed-body
		height: 70vh
		max-height: 390px
		overflow-y: auto

		.chat-feed-content
			padding: $body-margin-top 0 $body-margin-bottom

	.chat-feed-footer
		+white-shadow-top()

		align-items: center
		bottom: 0
		border-radius: 0 0 $border-radius-small $border-radius-small
		border-top: $border-light-gray
		height: $footer-height
		justify-content: center
		padding: $margin-small $margin-medium

		.loader
			margin-left: $margin-small

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
			width: 50%

		.zero-state-text
			color: $color-gray-dimmed
			font-size: $font-size-minus-1
			margin-top: $margin-big
			text-align: center
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import Dropdown from 'js/components/global/Dropdown'
	import ChatNotificationsToggle from 'js/components/notifications/feeds/chat/ChatNotificationsToggle'
	import ConversationsList from 'js/components/messages/ConversationsList'

	const setting = 'private_chat_nofitications'

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
			iconClass() {
				return this.isOn ? 'fa-comment' : 'fa-comment-o'
			},
			isOn() {
				return this.getSetting(setting)
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
		},
	}
</script>
