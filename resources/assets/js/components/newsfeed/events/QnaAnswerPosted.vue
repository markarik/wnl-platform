<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		odpowiedział/-a na pytanie <br>
		"{{ event.subject.text }}"
		<router-link :to="to">jedziesz szwagier</router-link>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import EventActor from '../EventActor'
	import {mapGetters} from 'vuex'

	export default {
		name: 'wnl-event-qna-answer-posted',
		props: ['event'],
		components: {
			'wnl-event-actor': EventActor
		},
		computed: {
			...mapGetters('course', ['courseId']),
			to() {
				return {
					name: 'screens',
					params: {
						screenId: this.event.context.screenId,
						lessonId: this.event.context.lessonId,
						courseId: this.courseId
					},
				}
			},
		}
	}
</script>
