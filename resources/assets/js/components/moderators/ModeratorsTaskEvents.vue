<template>
	<div>
		<h5>Ostatnia aktywność:</h5>
		<div class="moderators-notification">
			<div class="notification-content">
				<div class="notification-header">
					<span class="actor">{{ lastEvent.data.actors.full_name }}</span>
					<span class="action">{{ action }}</span>
					<span class="object" v-if="object">{{ object }}</span>
					<span class="context">{{contextInfo}}</span>
					<span class="object-text wrap" v-if="objectText">{{ objectText }}</span>
					<p class="subject wrap">{{text}}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.moderators-notification
		align-items: flex-start
		border: $border-light-gray
		border-radius: $border-radius-small
		display: flex
		flex: 1 auto
		font-size: $font-size-minus-1
		justify-content: space-between
		margin: $margin-base
		padding: $margin-medium
		position: relative
		transition: background-color $transition-length-base
	.actor
		font-weight: bold
	.notification-content
		flex: 1 auto
		padding: 0 $margin-medium
		.notification-header
			line-height: $line-height-minus
		.context,
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
			font-size: $font-size-base
			line-height: $line-height-minus
			margin: $margin-small 0
		.time
			align-items: center
			color: $color-background-gray
			display: flex
			flex-direction: row
			font-size: $font-size-minus-1
			justify-content: space-between
			margin-top: $margin-tiny
			&.is-mobile
				flex-direction: column
			.icon
				margin-right: $margin-tiny
			.actions
				.button + .button
					margin-left: $margin-small
			.status-text
				margin-right: $margin-small
				text-transform: uppercase
				&.in-progress
					color: $color-blue
				&.done
					color: $color-green
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
			padding: $margin-base
			transition: background-color $transition-length-base
			&:hover
				background-color: $color-background-lighter-gray
				transition: background-color $transition-length-base


</style>

<script>
import { decode } from 'he'
import { isEmpty, isObject, get, truncate, camelCase } from 'lodash'
import {mapGetters} from 'vuex'

export default {
	props: {
		events: {
			type: Array,
			required: true
		},
		routeContext: {
			type: String|Object
		}
	},
	computed: {
		...mapGetters('course', ['getLesson']),
		lastEvent() {
			return _.last(this.events)
		},
		text() {
			return decode(truncate(this.lastEvent.data.subject.text, {length: 256}))
		},
		objectText() {
			if (!this.lastEvent.data.objects) return false

			return decode(truncate(this.lastEvent.data.objects.text, {length: 256}))
		},
		hasMore() {
			return this.events.length > 1
		},
		action() {
			return this.$t(`notifications.events.${camelCase(this.lastEvent.data.event)}`)
		},
		object() {
			const objects = this.lastEvent.data.objects
			const subject = this.lastEvent.data.subject
			if (!objects && !subject) return false;

			// Qna Quesiton posted
			if (subject && !objects) {
				return this.$tc(`notifications.objects.${_.camelCase(subject.type)}`, 1)
			}

			return this.$tc(`notifications.objects.${_.camelCase(objects.type)}`, 1)
		},
		contextInfo() {
			if (!isObject(this.routeContext)) return ''

			const route = this.routeContext.name

			if (route === 'screens') {
				const lessonId = this.routeContext.params.lessonId
				const slide = this.routeContext.params.slide

				let contextInfo = this.$t('notifications.context.lesson', {
					lesson: truncate(this.getLesson(lessonId).name, {length: 30}),
				})

				if (get(this.message, 'objects.type') === 'slide' && slide) {
					contextInfo = `${this.$t('notifications.context.slide', {slide})} ${contextInfo}`
				}

				return contextInfo
			} else if (route === 'quizQuestion') {
				return this.$t('notifications.context.quizQuestion', {
					id: this.routeContext.params.id,
				})
			} else if (route.indexOf('help') > -1) {
				return this.$t('notifications.context.page', {
					page: this.$t(`routes.help.${route}`),
				})
			}

			return ''
		},

	}
};
</script>
