<template>
	<div class="wnl-dropdown" ref="dropdown">
		<div class="activator" :class="{ 'is-active' : isActive }" @click="toggle">
			<div class="flag" v-if="!!unseenCount">{{ unseenCount }}</div>
			<span class="icon">
				<i class="fa fa-bell"></i>
			</span>
		</div>
		<transition name="fade">
			<div class="box drawer" :class="{'is-mobile': isMobile}" v-if="isActive">
				<div class="personal-feed-header">
					<span class="feed-heading">Powiadomienia</span>
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
							<a v-if="hasMore" class="button is-small is-outlined"
								:class="{'is-loading': isFetching}"
								@click="loadMore"
							>
								Pokaż więcej
							</a>
							<span class="small text-dimmed has-text-centered" v-else>
								Wszystkie powiadomienia przeczytane <wnl-emoji name="+1"/>
							</span>
						</div>
					</div>
				</div>

				<div class="personal-feed-footer">
					<a class="link" @click="markAllAsRead(channel)">Oznacz wszystkie jako przeczytane</a>
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
		min-height: 100%
		position: relative

	.flag
		position: absolute
		border-radius: 20%
		font-size: $font-size-minus-4
		background: $color-red
		color: $color-white
		padding: 1px 3px 1px 3px
		top: 10px
		right: 3px
		border: thin solid white
		line-height: 100%

	.activator
		align-items: center
		color: $color-gray-dimmed
		cursor: pointer
		display: flex
		height: 100%
		justify-content: center
		margin-left: -$margin-small
		min-height: 100%
		padding: 0 $margin-small
		transition: background $transition-length-base

		&:hover
			background-color: $color-background-light-gray
			transition: background $transition-length-base

		&.is-active
			background-color: $color-background-light-gray
			color: $color-gray

			.username
				color: $color-gray
				font-weight: $font-weight-regular

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
</style>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import PersonalNotification from 'js/components/notifications/feeds/personal/PersonalNotification'
	import { CommentPosted, QnaAnswerPosted, ReactionAdded } from 'js/components/notifications/events'
	import { feed } from 'js/components/notifications/feed'

	export default {
		name: 'PersonalFeed',
		mixins: [feed],
		components: {
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-reaction-added': ReactionAdded,
		},
		data() {
			return {
				isActive: false,
				PersonalNotification
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			...mapGetters('notifications', {
				channel: 'userChannel',
				getUnseen: 'getUnseen',
			}),
			unseenCount() {
				return _.size(this.getUnseen(this.channel))
			},
		},
		methods: {
			...mapActions('notifications', [
				'markAllAsSeen',
				'markAllAsRead',
			]),
			toggle() {
				if (!this.isActive && !!this.unseenCount) {
					this.markAllAsSeen(this.channel)
				}
				this.isActive = !this.isActive
			},
			clickHandler({target}) {
				if (!this.$refs.dropdown.contains(target)) {
					this.isActive = false
				}
			}
		},
		watch: {
			'$route' (to, from) {
				this.isActive = false
			}
		},
		mounted() {
			document.addEventListener('click', this.clickHandler)
		},
		beforeDestroy() {
			document.removeEventListener('click', this.clickHandler)
		}
	}
</script>
