<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		zareagował/-a na {{ event.objects.type }}
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
