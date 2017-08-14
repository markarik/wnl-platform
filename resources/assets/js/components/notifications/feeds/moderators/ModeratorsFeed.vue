<template>
	<div class="moderators-feed">
		<div class="notification aligncenter" v-if="isEmpty">
			{{$t('notifications.moderators.isEmpty')}} <wnl-emoji name="tada"></wnl-emoji>
		</div>
		<div v-else>
			<div class="margin vertical">
				<a class="sorting-link" :class="{'is-active': sorting === 'oldest'}" @click="sorting = 'oldest'">
					{{$t('notifications.moderators.fromOldest')}}
				</a>
				<a class="sorting-link" :class="{'is-active': sorting === 'newest'}" @click="sorting = 'newest'">
					{{$t('notifications.moderators.fromNewest')}}
				</a>
			</div>
			<component :is="getEventComponent(message)"
				:message="message"
				:key="id"
				:notificationComponent="ModeratorsNotification"
				v-for="(message, id) in sorted"
				v-if="hasComponentForEvent(message)"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.sorting-link
		opacity: 0.6

		&.is-active
			opacity: 1
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
				sorting: 'oldest',
				ModeratorsNotification,
			}
		},
		computed: {
			...mapGetters('notifications', {
				channel: 'moderatorsChannel',
			}),
			fromOldest() {
				return _.reverse(_.clone(this.notifications))
			},
			sorted() {
				return this.sorting === 'oldest' ? this.fromOldest : this.notifications
			},
		},
	}
</script>
