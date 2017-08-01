<template>
	<div class="moderators-feed">
		<div class="notification aligncenter" v-if="isEmpty">
			{{$t('notifications.moderators.isEmpty')}} <wnl-emoji name="tada"></wnl-emoji>
		</div>
		<div v-else>
			<component :is="getEventComponent(message)"
				:message="message"
				:key="id"
				:notificationComponent="ModeratorsNotification"
				v-for="(message, id) in notifications"
				v-if="hasComponentForEvent(message)"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import ModeratorsNotification from 'js/components/notifications/feeds/moderators/ModeratorsNotification'
	import { CommentPosted, QnaAnswerPosted, QnaQuestionPosted } from 'js/components/notifications/events'
	import { feed } from 'js/components/notifications/feed'

	export default {
		name: 'ModeratorsFeed',
		mixins: [feed],
		components: {
			'wnl-event-comment-posted': CommentPosted,
			'wnl-event-qna-answer-posted': QnaAnswerPosted,
			'wnl-event-qna-question-posted': QnaQuestionPosted,
		},
		data() {
			return {
				ModeratorsNotification,
			}
		},
		computed: {
			...mapGetters('notifications', {
				channel: 'moderatorsChannel',
			}),
		},
	}
</script>
