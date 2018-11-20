<template>
	<div class="notification-wrapper">
		<div class="stream-notification" :class="{'is-read': isRead, 'deleted': deleted || resolved}">
			<div class="meta">
				<div class="avatar meta-actor" @click="showModal">
					<wnl-avatar :size="isMobile ? 'medium' : 'large'"
						:fullName="message.actors.full_name"
						:url="message.actors.avatar">
					</wnl-avatar>
				</div>
				<span class="icon is-small"><i class="fa" :class="icon"></i></span>
				<span class="meta-time">{{justDate}}</span>
				<span class="meta-time">{{justTime}}</span>
			</div>
			<div class="notification-content">
				<div class="notification-header">
					<span class="actor">{{ displayName }}</span>
					<span class="action">{{ action }}</span>
					<span class="object">{{ object }}</span>
					<span class="context">{{ contextInfo }}</span>
				</div>
				<div class="object-text wrap" v-if="objectText">{{ objectText }}</div>
				<div class="subject wrap" :class="{'unseen': !isSeen}" v-if="subjectText">{{ subjectText }}</div>
				<div class="time">
				</div>
			</div>
			<div class="link-symbol" :class="{'is-desktop': !isTouchScreen}">
				<div @click="dispatchMarkAsSeen" @contextmenu="dispatchMarkAsSeen">
					<router-link v-if="hasDynamicContext" :to="dynamicRoute">
						<span v-if="hasContext" class="icon go-to-link" :class="{'unseen': !isSeen}">
							<span v-if="loading" class="loader"></span>
							<i v-else class="fa fa-angle-right"></i>
						</span>
					</router-link>
					<router-link v-else-if="hasFullContext" :to="routeContext">
						<span v-if="hasContext" class="icon go-to-link" :class="{'unseen': !isSeen}">
							<span v-if="loading" class="loader"></span>
							<i v-else class="fa fa-angle-right"></i>
						</span>
					</router-link>
					<a v-else :href="routeContext">
						<span v-if="hasContext" class="icon go-to-link" :class="{'unseen': !isSeen}">
							<span v-if="loading" class="loader"></span>
							<i v-else class="fa fa-angle-right"></i>
						</span>
					</a>
				</div>
				<span class="icon is-small toggle-hidden" :title="$t('notifications.stream.hideNotification')" @click="toggleNotification">
					<span v-if="loading" class="loader"></span>
					<i v-else class="fa" :class="isRead ? 'fa-eye' : 'fa-eye-slash'"></i>
				</span>
			</div>
		</div>
		<div class="delete-message" v-if="deleted" v-t="'notifications.messages.deleted'"/>
		<div class="delete-message" v-if="resolved" v-t="'notifications.messages.resolved'"/>
		<wnl-modal :isModalVisible="isVisible" @closeModal="closeModal" v-if="isVisible">
			<wnl-user-profile-modal :author="message.actors"/>
		</wnl-modal>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.stream-notification
		align-items: flex-start
		background: $color-white
		border: $border-light-gray
		display: flex
		font-size: $font-size-minus-1
		justify-content: space-between
		margin-bottom: $margin-big
		padding: $margin-medium
		position: relative

		&.is-read
			opacity: 0.75

			.link-symbol

				.icon
					color: $color-inactive-gray

					&.unseen
						border-color: $color-inactive-gray

	.meta
		align-items: center
		display: flex
		flex-direction: column
		margin: -$margin-small 0
		padding: $margin-small 0

		.meta-actor
			margin-bottom: $margin-small

		.meta-time
			color: $color-background-gray
			font-size: $font-size-minus-3
			line-height: $line-height-minus
			text-align: center

		.icon
			color: $color-background-gray
			text-align: center
			margin-bottom: $margin-small

	.notification-content
		flex: 1 auto
		padding: 0 $margin-base 0 $margin-medium

		.notification-header
			line-height: $line-height-minus
			margin-bottom: $margin-tiny

			.actor,
			.context
				font-weight: $font-weight-bold

		.object-text
			color: $color-gray-dimmed
			font-style: italic
			line-height: $line-height-minus
			margin-bottom: $margin-tiny

		.subject
			border-left: 2px solid $color-inactive-gray
			font-size: $font-size-base
			margin-top: $margin-base
			padding-left: $margin-medium
			padding-bottom: $margin-tiny

			&.unseen
				border-color: $color-ocean-blue

		.time
			color: $color-background-gray
			font-size: $font-size-minus-2
			margin-top: $margin-tiny

			.icon
				margin-right: $margin-tiny

	.link-symbol
		align-items: center
		display: flex
		flex: 0
		flex-direction: column
		height: 100%
		justify-content: space-between
		min-height: 100%

		&.is-desktop

			.icon
				cursor: pointer
				transition: background $transition-length-base

				&:hover
					background: $color-background-lighter-gray
					transition: background $transition-length-base

		.icon
			border: $border-light-gray
			border-radius: $border-radius-small
			color: $color-background-gray
			padding: $margin-base

			&.unseen
				border-color: $color-ocean-blue
				color: $color-ocean-blue

			&.go-to-link
				.fa
					padding: 0 0 $margin-tiny 1px

			&.toggle-hidden
				margin-top: $margin-base
				padding: $margin-medium
</style>

<script>
	import { truncate, camelCase, get } from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import Avatar from 'js/components/global/Avatar'
	import UserProfileModal from 'js/components/users/UserProfileModal'
	import Modal from 'js/components/global/Modal'
	import { notification } from 'js/components/notifications/notification'
	import { justTimeFromS, justMonthAndDayFromS } from 'js/utils/time'
	import { sanitizeName } from 'js/store/modules/users'

	export default {
		name: 'StreamNotification',
		mixins: [notification],
		components: {
			'wnl-avatar': Avatar,
			'wnl-modal': Modal,
			'wnl-user-profile-modal': UserProfileModal
		},
		props: {
			icon: {
				required: true,
				type: String
			},
		},
		data() {
			return {
				objectTextLength: 200,
				subjectTextLength: 300,
				isVisible: false
			}
		},
		computed: {
			...mapGetters(['currentUserId', 'isMobile', 'isTouchScreen']),
			displayName() {
				return sanitizeName(this.message.actors.display_name)
			},
			action() {
				return this.$t(`notifications.events.${camelCase(this.message.event)}`)
			},
			justDate() {
				return justMonthAndDayFromS(this.message.timestamp)
			},
			justTime() {
				return justTimeFromS(this.message.timestamp)
			},
			object() {
				const objects = this.message.objects
				const subject = this.message.subject
				const type = !!objects ? objects.type : subject.type
				const choice = !!objects ? this.currentUserId === objects.author ? 2 : 1 : 1

				return this.$tc(`notifications.objects.${camelCase(type)}`, choice)
			},
		},
		methods: {
			showModal() {
				this.isVisible = true
			},
			closeModal() {
				this.isVisible = false
			},
			...mapActions('notifications', ['markAsUnread']),
			toggleNotification() {
				this.loading = true

				if (this.isRead) {
					return this.markAsUnread({notification: this.message, channel: this.channel})
						.then(() => this.loading = false)
				}

				return this.markAsRead({notification: this.message, channel: this.channel})
					.then(() => this.loading = false)
			},
			dispatchMarkAsSeen() {
				if(!this.hasContext) return false;

				this.loading = true

				if (!this.isSeen) {
					this.markAsSeen({notification: this.message, channel: this.channel})
						.then(() => {
							this.loading = false
						})
				} else {
					this.loading = false
				}
			},
		},
	}
</script>
