<template>
	<div class="personal-notification" @click="goToContext">
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
		<div class="link-symbol">
			<span v-if="hasContext" class="icon" :class="{'unread': !isRead}">
				<i v-if="loading" class="loader"></i>
				<i v-else class="fa fa-angle-right"></i>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.personal-notification
		align-items: flex-start
		border-bottom: $border-light-gray
		display: flex
		font-size: $font-size-minus-2
		justify-content: space-between
		padding: $margin-medium
		position: relative
		transition: background-color $transition-length-base

		&:hover
			background: $color-background-lighter-gray
			cursor: pointer
			transition: background-color $transition-length-base

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
			font-size: $font-size-minus-1
			line-height: $line-height-minus
			margin-top: $margin-tiny

		.time
			color: $color-background-gray
			font-size: $font-size-minus-3
			margin-top: $margin-tiny

			.icon
				margin-right: $margin-tiny

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
			objectText() {
				if (!this.object) return false;

				return truncate(this.message.objects.text, {length: 75})
			},
			subjectText() {
				if (!this.message.subject) return false;

				return truncate(this.message.subject.text, {length: 150})
			}
		}
	}
</script>
