<template>
	<div class="wnl-dropdown">
		<div class="activator" :class="{ 'is-active' : isActive }" @click="toggle">
			<div class="flag" v-if="hasUnseen">{{ unseenCount }}</div>
			<span class="icon">
				<i class="fa fa-bell"></i>
			</span>
		</div>
		<transition name="fade">
			<div class="box drawer" v-if="isActive">
				<div class="level wnl-screen-title">
					<strong>Powiadomienia</strong>
					<a class="link" @click="markAllAsRead(userChannel)">Oznacz wszystkie jako przeczytane</a>
				</div>

				<div class="notification aligncenter" v-if="empty">
					Nic tu nie ma ¯\_(ツ)_/¯
				</div>
				<div v-else>
					<wnl-newsfeed-event
							v-for="(event, index) in notifications"
							:event="event"
							:key="index"
							:channel="userChannel"
					></wnl-newsfeed-event>

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
		color: $color-gray
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
			color: $color-darkest-gray

			.username
				color: $color-darkest-gray
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
import {mapGetters, mapActions} from 'vuex';
import Event from 'js/components/user/notifications/Event';

export default {
	name: 'wnl-user-notifications',
	components: {
		'wnl-newsfeed-event': Event,
	},
	data() {
		return {
			isActive: false,
		};
	},
	computed: {
		...mapGetters('notifications', [
			'getSortedNotifications',
			'isLoading',
			'getUnseen',
			'userChannel',
			'getOldestNotification'
		]),
		empty() {
			return !this.isLoading && _.size(this.notifications) === 0;
		},
		hasUnseen() {
			return this.unseenCount > 0;
		},
		unseenCount() {
			return _.size(this.getUnseen(this.userChannel));
		},
		notifications() {
			return this.getSortedNotifications(this.userChannel);
		}
	},
	methods: {
		...mapActions('notifications', [
			'markAllAsSeen',
			'markAllAsRead',
			'pullNotifications'
		]),
		toggle() {
			if (!this.isActive && this.hasUnseen) {
				this.markAllAsSeen(this.userChannel);
			}
			this.isActive = !this.isActive;
		},
		loadMore() {
			const channel = this.userChannel;
			const oldest = this.getOldestNotification(channel);
			const options = {
				limit: 15,
				olderThan: oldest.timestamp,
			};
			this.pullNotifications([channel, options]);
		}
	},
	watch: {
		'$route' (to, from) {
			this.isActive = false;
		}
	},
};
</script>
