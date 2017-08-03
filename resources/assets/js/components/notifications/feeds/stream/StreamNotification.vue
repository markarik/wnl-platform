<template>
	<div class="notification-wrapper">
		<div class="stream-notification" @click="markAsReadAndGo" :class="{'deleted': deleted}">
			<div class="meta">
				<wnl-event-actor :size="isMobile ? 'medium' : 'large'" class="meta-actor" :message="message"/>
				<span class="icon is-small"><i class="fa" :class="icon"></i></span>
				<span class="meta-time">{{justDate}}</span>
				<span class="meta-time">{{justTime}}</span>
			</div>
			<div class="notification-content">
				<div class="notification-header">
					<span class="actor">{{ message.actors.full_name }}</span>
					<span class="action">{{ action }}</span>
					<span class="object">{{ object }}</span>
					<span class="context">{{ contextInfo }}</span>
				</div>
				<div class="object-text wrap" v-if="objectText">{{ objectText }}</div>
				<div class="subject wrap" :class="{'unread': !isRead}" v-if="subjectText">{{ subjectText }}</div>
				<div class="time">
				</div>
			</div>
			<div class="link-symbol">
				<span v-if="hasContext" class="icon" :class="{'unread': !isRead}">
					<i v-if="loading" class="loader"></i>
					<i v-else class="fa fa-angle-right"></i>
				</span>
			</div>
		</div>
		<div class="delete-message" v-if="deleted">{{$t('notifications.messages.deleted')}}</div>
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
		transition: background-color $transition-length-base

		&:hover
			background: $color-background-lighter-gray
			cursor: pointer
			transition: background-color $transition-length-base

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
			border-left: $border-thick solid $color-inactive-gray
			font-size: $font-size-base
			margin-top: $margin-base
			padding-left: $margin-medium

			&.unread
				border-color: $color-ocean-blue

		.time
			color: $color-background-gray
			font-size: $font-size-minus-2
			margin-top: $margin-tiny

			.icon
				margin-right: $margin-tiny

	.link-symbol
		display: flex
		flex: 0

		.icon
			color: $color-inactive-gray

			&.unread
				color: $color-ocean-blue
</style>

<script>
	import { truncate } from 'lodash'
	import { mapGetters } from 'vuex'

	import Actor from 'js/components/notifications/Actor'
	import { notification } from 'js/components/notifications/notification'
	import { justTimeFromS, justMonthAndDayFromS } from 'js/utils/time'

	export default {
		name: 'StreamNotification',
		mixins: [notification],
		components: {
			'wnl-event-actor': Actor,
		},
		props: {
			icon: {
				required: true,
				type: String
			},
		},
		computed: {
			...mapGetters(['currentUserId', 'isMobile']),
			action() {
				return this.$t(`notifications.events.${_.camelCase(this.message.event)}`)
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

				return this.$tc(`notifications.objects.${_.camelCase(type)}`, choice)
			},
			objectText() {
				if (!this.message.objects) return false

				return truncate(this.message.objects.text, {length: 200})
			},
			subjectText() {
				if (!this.message.subject) return false

				return truncate(this.message.subject.text, {length: 300})
			},
		},
		methods: {
			dispatchGoToContext() {
				this.goToContext()
				this.loading = false
			},
			markAsReadAndGo() {
				if(!this.hasContext) return false;

				this.loading = true

				if (!this.isRead) {
					this.markAsRead({notification: this.message, channel: this.channel})
						.then(() => {
							this.dispatchGoToContext()
						})
				} else {
					this.dispatchGoToContext()
				}
			},
		},
	}
</script>
