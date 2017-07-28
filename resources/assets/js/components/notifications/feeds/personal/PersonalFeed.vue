<template>
	<div class="dropdown-container">
		<wnl-dropdown :options="{isWide: true}" @toggled="toggle">
			<div slot="activator" class="notifications-toggle"
				:class="{ 'is-active': isActive, 'is-off': !isOn, 'is-desktop': !isTouchScreen }">
				<div v-if="isOn && !!unseenCount" class="counter">{{ unseenCount }}</div>
				<span class="icon">
					<i class="fa" :class="iconClass"></i>
				</span>
			</div>
			<div slot="content">
				<div class="personal-feed-header">
					<span class="feed-heading">{{$t('notifications.personal.heading')}}</span>
					<wnl-notifications-toggle/>
				</div>

				<div class="personal-feed-body">
					<div class="zero-state" v-if="isEmpty">
						<img class="zero-state-image"
							title="$t('notifications.personal.zeroStateImage')"
							:alt="$t('notifications.personal.zeroStateImage')"
							:src="zeroStateImage">
						<p class="zero-state-text">
							{{$t('notifications.personal.zeroState')}}
						</p>
					</div>
					<div v-else class="personal-feed-content">
						<component :is="getEventComponent(message)"
							:message="message"
							:key="id"
							:notificationComponent="PersonalNotification"
							v-for="(message, id) in notifications"
							v-if="hasComponentForEvent(message)"
						/>
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

				<div class="personal-feed-footer" v-if="unreadCount > 0">
					<a class="link" @click="allRead">{{$t('notifications.personal.markAllAsRead')}}</a>
					<span v-if="allReadLoading" class="loader"></span>
				</div>
			</div>
		</wnl-dropdown>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$header-height: 40px
	$footer-height: 40px
	$body-margin-top: $header-height
	$body-margin-bottom: $footer-height + $margin-big

	.dropdown-container
		align-items: center
		display: flex
		height: $navbar-height
		justify-content: center
		width: $navbar-height

	.notifications-toggle
		align-items: center
		color: $color-gray-dimmed
		cursor: pointer
		display: flex
		height: 100%
		justify-content: center
		margin-left: -$margin-small
		min-height: 100%
		transition: background $transition-length-base

		&.is-desktop:hover
			background-color: $color-background-light-gray
			transition: background $transition-length-base

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

	.personal-feed
		position: relative

	.personal-feed-header,
	.personal-feed-footer
		align-items: center
		background: $color-white
		display: flex
		position: absolute
		width: 100%
		z-index: $z-index-overlay

	.personal-feed-header
		border-radius: $border-radius-small $border-radius-small 0 0
		border-bottom: $border-light-gray
		height: $header-height
		justify-content: space-between
		padding: $margin-small $margin-medium
		top: 0

		.feed-heading
			font-size: $font-size-minus-2
			font-weight: $font-weight-bold
			text-transform: uppercase

	.personal-feed-body
		height: 70vh
		max-height: 390px
		overflow-y: auto

		.personal-feed-content
			padding: $body-margin-top 0 $body-margin-bottom

		.show-more
			align-items: center
			display: flex
			justify-content: center
			margin: $margin-base

	.personal-feed-footer
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
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import Dropdown from 'js/components/global/Dropdown'
	import NotificationsToggle from 'js/components/notifications/feeds/personal/NotificationsToggle'
	import PersonalNotification from 'js/components/notifications/feeds/personal/PersonalNotification'
	import { CommentPosted, QnaAnswerPosted, ReactionAdded } from 'js/components/notifications/events'
	import { feed } from 'js/components/notifications/feed'
	import { getImageUrl } from 'js/utils/env'

	const setting = 'notify_live'

	export default {
		name: 'PersonalFeed',
		mixins: [feed],
		components: {
			'wnl-dropdown': Dropdown,
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-reaction-added': ReactionAdded,
			'wnl-notifications-toggle': NotificationsToggle,
		},
		data() {
			return {
				allReadLoading: false,
				isActive: false,
				limit: 15,
				PersonalNotification,
			}
		},
		computed: {
			...mapGetters(['isTouchScreen', 'getSetting']),
			...mapGetters('notifications', {
				channel: 'userChannel',
				getUnseen: 'getUnseen',
				getUnread: 'getUnread',
			}),
			canShowMore() {
				return this.hasMore(this.channel)
			},
			iconClass() {
				return this.isOn ? 'fa-bell' : 'fa-bell-slash'
			},
			isOn() {
				return this.getSetting(setting)
			},
			showEndInfo() {
				return this.totalNotifications > this.limit && !this.canShowMore
			},
			unseenCount() {
				return _.size(this.getUnseen(this.channel))
			},
			unreadCount() {
				return _.size(this.getUnread(this.channel))
			},
			zeroStateImage() {
				return getImageUrl('notifications-zero.png')
			},
		},
		methods: {
			...mapActions('notifications', [
				'markAllAsSeen',
				'markAllAsRead',
			]),
			allRead() {
				this.allReadLoading = true
				this.markAllAsRead(this.channel)
					.then(() => {
						this.markAllAsSeen(this.channel)
						this.allReadLoading = false
					})
			},
			toggle(isActive) {
				this.isActive = isActive
				this.markAllAsSeen(this.channel)
			},
		},
	}
</script>
