<template>
	<div class="wnl-dropdown">
		<div class="activator" :class="{ 'is-active' : isActive }" @click="toggle">
			<div class="flag" v-if="!!unseenCount">{{ unseenCount }}</div>
			<span class="icon">
				<i class="fa fa-bell"></i>
			</span>
		</div>
		<transition name="fade">
			<div class="box drawer" v-if="isActive">
				<div class="level wnl-screen-title">
					<strong>Powiadomienia</strong>
					<a class="link" @click="markAllAsRead(channel)">Oznacz wszystkie jako przeczytane</a>
				</div>

				<div class="notification aligncenter" v-if="isEmpty">
					Nic tu nie ma ¯\_(ツ)_/¯
				</div>
				<div v-else>
					<wnl-personal-notification
						v-for="(message, id) in notifications"
						:channel="channel"
						:message="message"
						:key="id"
					/>
					<a class="button" @click="loadMore">Wincyj!</a>
				</div>
			</div>
		</transition>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

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
		right: 0
		position: absolute
		top: 95%
		z-index: 100
		width: 440px

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
</style>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import PersonalNotification from 'js/components/notifications/feeds/personal/PersonalNotification'
	import { feed } from 'js/components/notifications/feed'

	export default {
		name: 'PersonalFeed',
		mixins: [feed],
		components: {
			'wnl-personal-notification': PersonalNotification,
		},
		data() {
			return {
				isActive: false,
			}
		},
		computed: {
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
		},
		watch: {
			'$route' (to, from) {
				this.isActive = false
			}
		},
	}
</script>
