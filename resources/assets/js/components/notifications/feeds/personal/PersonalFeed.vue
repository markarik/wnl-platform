<template>
	<div class="wnl-dropdown" ref="dropdown">
		<div class="activator" :class="{ 'is-active' : isActive, 'is-off': !isOn }" @click="toggle">
			<div v-if="isOn && !!unseenCount" class="flag">{{ unseenCount }}</div>
			<span class="icon">
				<i class="fa" :class="iconClass"></i>
			</span>
		</div>
		<transition name="fade">
			<div class="box drawer" :class="{'is-mobile': isMobile}" v-if="isActive">
				<div class="personal-feed-header">
					<span class="feed-heading">Powiadomienia</span>
					<wnl-notifications-toggle/>
				</div>

				<div class="personal-feed-body">
					<div class="notification aligncenter" v-if="isEmpty">
						Nic tu nie ma ¯\_(ツ)_/¯
					</div>
					<div v-else>
						<component :is="getEventComponent(message)"
							:channel="channel"
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
								Pokaż więcej
							</a>
							<span v-else-if="showEndInfo" class="small text-dimmed has-text-centered">
								To już wszystko! <wnl-emoji name="+1"/>
							</span>
						</div>
					</div>
				</div>

				<div class="personal-feed-footer" v-if="unreadCount > 0">
					<a class="link" @click="allRead">Oznacz wszystkie jako przeczytane</a>
					<span v-if="allReadLoading" class="loader"></span>
				</div>
			</div>
		</transition>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$header-height: 40px
	$footer-height: 40px
	$body-margin-top: $header-height
	$body-margin-bottom: $footer-height + $margin-big

	.wnl-dropdown
		height: 100%
		max-width: $navbar-height
		min-height: 100%
		min-width: $navbar-height
		position: relative
		width: $navbar-height

	.flag
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

	.activator
		align-items: center
		color: $color-gray-dimmed
		cursor: pointer
		display: flex
		height: 100%
		justify-content: center
		margin-left: -$margin-small
		min-height: 100%
		transition: background $transition-length-base

		&:hover
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

	.drawer
		+shadow()

		max-width: 100vw
		padding: 0
		position: absolute
		right: 0
		top: 95%
		width: 440px
		z-index: 100

		&.is-mobile
			border-radius: 0
			position: fixed
			top: $navbar-height

	.metadata,
	.drawer-item
		padding: $margin-small $margin-base
		text-align: right
		white-space: nowrap

	.drawer-item
		font-size: $font-size-minus-1

		&:last-child
			border: 0

	.drawer-link,
	.drawer-link.is-active
		font-weight: $font-weight-regular

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
		padding: $body-margin-top 0 $body-margin-bottom
		max-height: 400px
		overflow-y: auto

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
</style>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import PersonalNotification from 'js/components/notifications/feeds/personal/PersonalNotification'
	import NotificationsToggle from 'js/components/notifications/feeds/personal/NotificationsToggle'
	import { CommentPosted, QnaAnswerPosted, ReactionAdded } from 'js/components/notifications/events'
	import { feed } from 'js/components/notifications/feed'

	const setting = 'notify_live'

	export default {
		name: 'PersonalFeed',
		mixins: [feed],
		components: {
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
			...mapGetters(['isMobile', 'getSetting']),
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
						this.allReadLoading = false
					})
			},
			clickHandler({target}) {
				if (!this.$refs.dropdown.contains(target)) {
					this.isActive = false
				}
			},
			toggle() {
				this.markAllAsSeen(this.channel)
				this.isActive = !this.isActive
			},
		},
		watch: {
			'$route' (to, from) {
				this.isActive = false
			},
		},
		mounted() {
			document.addEventListener('click', this.clickHandler)
		},
		beforeDestroy() {
			document.removeEventListener('click', this.clickHandler)
		}
	}
</script>
