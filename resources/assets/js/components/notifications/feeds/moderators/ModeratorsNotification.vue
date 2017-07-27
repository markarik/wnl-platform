<template>
	<div class="notification-container">
		<div class="moderators-notification" :class="{'is-read': isRead, 'is-desktop': !isTouchScreen}" @click="goToContext">
			<div class="actor">
				<wnl-event-actor :message="message"/>
			</div>
			<div class="notification-content">
				<div class="notification-header">
					<span class="actor">{{ message.actors.full_name }}</span>
					<span class="action">{{ action }}</span>
					<span class="object" v-if="object">{{ object }}</span>
					<span class="object-text" v-if="objectText">{{ objectText }}</span>
				</div>
				<div class="subject" v-if="subjectText">{{ subjectText }}</div>
				<div class="time">
					<span class="icon is-tiny">
						<i class="fa" :class="icon"></i>
					</span> {{ formattedTime }}
				</div>
			</div>
		</div>
		<div class="link-symbol">
			<span class="icon checkmark" v-if="!isRead"
				@click="dispatchMarkAsRead">
				<span v-if="loading" class="loader"></span>
				<i v-else class="fa fa-check"></i>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.notification-container
		display: flex
		justify-content: space-between
		margin-bottom: $margin-big

	.moderators-notification
		align-items: flex-start
		border: $border-light-gray
		border-radius: $border-radius-small
		cursor: pointer
		display: flex
		flex: 1 auto
		justify-content: space-between
		margin-right: $margin-base
		padding: $margin-medium
		position: relative
		transition: background-color $transition-length-base

		&.is-desktop:hover
			background-color: $color-background-lighter-gray
			transition: background-color $transition-length-base

		&.is-read
			opacity: 0.5
			transition: opacity $transition-length-base

			&:hover
				background-color: initial
				opacity: 1
				transition: opacity $transition-length-base

	.actor
		font-weight: bold

	.notification-content
		flex: 1 auto
		padding: 0 $margin-medium

		.notification-header
			line-height: $line-height-minus

		.object
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
			font-size: $font-size-plus-1
			line-height: $line-height-minus
			margin: $margin-small 0

		.time
			color: $color-background-gray
			font-size: $font-size-minus-1
			margin-top: $margin-tiny

			.icon
				margin-right: $margin-tiny

	.link-symbol
		align-items: flex-end
		display: flex
		flex: 0
		flex-direction: column
		justify-content: space-between
		height: 100%

		.checkmark
			border: $border-light-gray
			border-radius: $border-radius-small
			color: $color-green
			cursor: pointer
			padding: $margin-big
			transition: background-color $transition-length-base

			&:hover
				background-color: $color-background-lighter-gray
				transition: background-color $transition-length-base
</style>

<script>
	import { truncate } from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import Actor from 'js/components/notifications/Actor'
	import { notification } from 'js/components/notifications/notification'

	export default {
		name: 'ModeratorsNotification',
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
			...mapGetters(['isTouchScreen']),
			action() {
				return this.$t(`notifications.events.${_.camelCase(this.message.event)}`)
			},
			object() {
				const objects = this.message.objects
				if (!objects) return false;

				return this.$tc(`notifications.objects.${_.camelCase(objects.type)}`, 1)
			},
			objectText() {
				if (!this.object) return false;

				return truncate(this.message.objects.text, {length: 150})
			},
			subjectText() {
				if (!this.message.subject) return false;

				return truncate(this.message.subject.text, {length: 250})
			}
		},
		methods: {
			...mapActions('notifications', ['markAsRead']),
			dispatchMarkAsRead() {
				this.loading = true
				this.markAsRead({notification: this.message, channel: this.channel})
					.then(() => this.loading = false)
			}
		},
	}
</script>
