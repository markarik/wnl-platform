<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		odpowiedział/-a na pytanie <br>
		"{{ event.subject.text }}"
		<p>
			Treść pytania:<br>
			{{ event.objects.text }}
		</p>
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
			href() {
				return this.hasContext ? this.to : this.event.referer
			},
			to() {
				return this.$router.resolve(this.routeParams).href
			},
			hasContext() {
				return false
			},
			routeParams() {
				return {
					name: 'screens',
					params: {
						screenId: this.event.context.screenId,
						lessonId: this.event.context.lessonId,
						courseId: this.courseId
					}
				}
			}
		}
	}
</script>
