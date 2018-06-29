<template>
	<div class="notification-wrapper">
		<div class="personal-notification" @click="markAsReadAndGo" :class="{'deleted': deleted || resolved}">
			<div class="actor">
				<wnl-event-actor :message="message" :disableLink="true"/>
			</div>
			<div class="notification-content">
				<div class="notification-header">
					<span class="actor">{{ displayName }}</span>
					<span class="action">{{ action }}</span>
					<span class="object" v-if="object">{{ object }}</span>
					<span class="context" v-if="contextInfo">{{ contextInfo }}</span>
					<span class="object-text wrap" v-if="objectText">{{ objectText }}</span>
				</div>
				<div class="subject wrap" v-if="subjectText">{{ subjectText }}</div>
				<div class="time">
					<span class="icon is-small">
						<i class="fa" :class="icon"></i>
					</span>{{ formattedTime }}
				</div>
			</div>
			<div class="link-symbol">
				<span v-if="hasContext" class="icon" :class="{'unread': !isRead}">
					<i v-if="loading" class="loader"></i>
					<i v-else class="fa fa-angle-right"></i>
				</span>
			</div>
		</div>
		<div class="delete-message" v-if="deleted" v-t="'notifications.messages.deleted'"/>
		<div class="delete-message" v-if="resolved" v-t="'notifications.messages.resolved'"/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.personal-notification
		align-items: flex-start
		border-bottom: $border-light-gray
		display: flex
		font-size: $font-size-minus-1
		justify-content: space-between
		padding: $margin-medium
		position: relative
		transition: background-color $transition-length-base

		&:hover
			background: $color-background-lighter-gray
			cursor: pointer
			transition: background-color $transition-length-base

	.actor
		font-weight: $font-weight-bold

	.notification-content
		flex: 1 auto
		padding: 0 $margin-medium

		.notification-header
			line-height: $line-height-minus

		.context
			font-weight: $font-weight-bold

		.object-text,
		.subject

			&::before
				content: '« '

			&::after
				content: ' »'

		.object-text
			color: $color-gray-dimmed

		.subject
			font-size: $font-size-base
			line-height: $line-height-minus
			margin-top: $margin-small

		.time
			color: $color-background-gray
			font-size: $font-size-minus-2
			margin-top: $margin-small

			.icon
				margin-right: $margin-small

	.link-symbol
		display: flex
		flex: 0
		width:

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
	import { sanitizeName } from 'js/store/modules/users'

	export default {
		name: 'PersonalNotification',
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
			...mapGetters(['currentUserId']),
			displayName() {
				return sanitizeName(this.message.actors.display_name)
			},
			action() {
				return this.$t(`notifications.events.${_.camelCase(this.message.event)}`)
			},
			object() {
				const objects = this.message.objects
				if (!objects) return false;

				return this.$tc(
					`notifications.objects.${_.camelCase(objects.type)}`,
					this.currentUserId === objects.author ? 2 : 1
				)
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
