<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		skomentował/-a {{ event.objects.type }}
		<br>
		"{{ event.subject.text }}"
		<a :href="event.referer" target="_blank" v-if="!hasContext">jedziesz szwagier</a>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import EventActor from '../EventActor'

	export default {
		name: 'wnl-event-qna-answer-posted',
		props: ['event'],
		components: {
			'wnl-event-actor': EventActor
		},
		computed: {
			hasContext() {
				return this.event.hasOwnProperty('context')
			}
		}
	}
</script>
