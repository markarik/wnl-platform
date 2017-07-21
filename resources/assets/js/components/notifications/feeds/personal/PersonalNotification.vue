<template>
	<div class="personal-notification" v-if="hasComponent" @click="goToContext">
		<div class="author">
			<wnl-event-actor :message="message"/>
		</div>
		<component :is="componentName" :message="message" @contextReady="setContext">
			<!-- <template scope="props">
				<div>
					{{ message.actors.full_name }} {{ props.action }} {{ message.object.type }} {{ message.object.text }}
				</div>
				<div v-if="message.subject.text">{{ message.subject.text }}</div>
				<small class="time">
					<span class="icon is-small">
						<i class="fa" :class="{{ props.icon }}"></i>
					</span> {{ formattedTime }}
				</small>
				<div>Punkcja</div>
			</template> -->
		</component>
		<div class="link-symbol">
			<span class="icon" :class="{'has-text-dimmed': isRead}">
				<i class="fa fa-angle-right"></i>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.personal-notification
		border-bottom: $border-light-gray
		padding: $margin-small
		position: relative

		&:hover
			background: $color-background-lighter-gray
			cursor: pointer

		.time
			color: $color-inactive-gray

		.wnl-avatar-small
			display: inline-flex

		.unread
			position: absolute
			background: $color-ocean-blue
			color: $color-white
			top: 10px
			right: 10px
			width: 10px
			height: 10px
			border-radius: 100%
</style>

<script>
	import Actor from 'js/components/notifications/Actor'
	import { CommentPosted, QnaAnswerPosted, QnaQuestionPosted, ReactionAdded } from 'js/components/notifications/events'
	import { notification } from 'js/components/notifications/notification'

	export default {
		name: 'PersonalNotification',
		components: {
			'wnl-event-actor': Actor,
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-qna-question-posted': QnaQuestionPosted,
			'wnl-event-reaction-added': ReactionAdded,
		},
		mixins: [notification],
	}
</script>
