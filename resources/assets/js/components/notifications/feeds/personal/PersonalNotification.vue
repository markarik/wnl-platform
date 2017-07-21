<template>
	<div class="personal-notification" v-if="hasComponent" @click="goToContext">
		<div class="actor">
			<wnl-event-actor :message="message"/>
		</div>
		<component class="notification-content" :is="componentName" :message="message" @contextReady="setContext">
			<template scope="props">
				<div>{{ message.actors.full_name }} {{ props.action }} {{ message.objects.type }} {{ message.objects.text }}</div>
				<div v-if="message.subject.text">{{ message.subject.text }}</div>
				<small class="time">
					<span class="icon is-small">
						<i class="fa" :class="props.icon"></i>
					</span> {{ formattedTime }}
				</small>
			</template>
		</component>
		<div class="link-symbol">
			<span v-if="hasContext" class="icon" :class="{'unread': !isRead}">
				<i class="fa fa-angle-right"></i>
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
		justify-content: space-between
		padding: $margin-medium
		position: relative
		transition: background-color $transition-length-base

		&:hover
			background: $color-background-lighter-gray
			cursor: pointer
			transition: background-color $transition-length-base

	.actor
		margin-top: 5px

	.notification-content
		flex: 1 auto
		padding: 0 $margin-medium

		.time
			color: $color-background-gray

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
	import Actor from 'js/components/notifications/Actor'
	import { CommentPosted, QnaAnswerPosted, ReactionAdded } from 'js/components/notifications/events'
	import { notification } from 'js/components/notifications/notification'

	export default {
		name: 'PersonalNotification',
		mixins: [notification],
		components: {
			'wnl-event-actor': Actor,
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-reaction-added': ReactionAdded,
		},
	}
</script>
