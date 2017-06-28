<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		skomentował/-a {{ event.objects.type }}
		<br>
		"{{ event.subject.text }}"
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
		}
	}
</script>
