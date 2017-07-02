<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		skomentował/-a {{ resolveType(event.objects.type) }}
		<p>
			"{{ event.subject.text }}"
		</p>
		<p v-if="event.objects.type === 'quiz_question'">
			Numer pytania: {{ event.objects.id }}
		</p>
		<p>
			<a :href="href" target="_blank">jedziesz szwagier</a>
		</p>
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
		data() {
			return {
				'typesMap': {
					'quiz_question': 'pytanie z quizu',
					'qna_answer': 'odpowiedź na pytanie',
					'slide': 'slajd',
				}
			}
		},
		computed: {
			...mapGetters('course', ['courseId']),
			hasContext() {
				return this.event.hasOwnProperty('context')
			},
			href() {
				return this.hasContext ? this.to : this.event.referer
			},
			to() {
				return this.$router.resolve(this.getObjectRoute(this.event.objects.type)).href
			}
		},
		methods: {
			getObjectRoute(type) {
				return {
					'qna_answer': {
						name: 'screens',
						params: {
							screenId: this.event.context.screenId,
							lessonId: this.event.context.lessonId,
							courseId: this.courseId
						},
					}
				}[type]
			},
			resolveType(type) {
				return this.typesMap[type] || type
			}
		}
	}
</script>
