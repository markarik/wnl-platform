<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		skomentował/-a {{ event.objects.type }}
		<br>
		"{{ event.subject.text }}"
		<a :href="href" target="_blank">jedziesz szwagier</a>
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
			hasContext() {
				return this.event.hasOwnProperty('context')
			},
			href() {
				return this.hasContext ? this.to : event.referer
			},
			to() {
				return this.$router.resolve(this.objectRoute(this.event.objects.type)).href
			}
		},
		methods: {
			objectRoute(type) {
				const routes = {
					'qna_answer': {
						name: 'screens',
						params: {
							screenId: this.event.context.screenId,
							lessonId: this.event.context.lessonId,
							courseId: this.courseId
						},
					},
				}

				return routes[type]
			}
		}
	}
</script>
