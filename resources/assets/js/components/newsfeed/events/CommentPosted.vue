<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		skomentował/-a {{ event.objects.type }}
		<br>
		"{{ event.subject.text }}"
		<router-link :to="to" v-if="hasContext">jedziesz szwagier</router-link>
		<a :href="event.referer" target="_blank" v-else="">jedziesz szwagier</a>
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
			to() {
				return this.objectRoute(this.event.objects.type)
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
