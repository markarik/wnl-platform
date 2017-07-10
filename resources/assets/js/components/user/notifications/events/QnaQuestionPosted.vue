<template lang="html">
	<div>
		<wnl-event-actor :event="event"/>
		zadał/-a pytanie <br>
		"{{ event.subject.text }}"
		<a :href="event.referer" target="_blank">zabierz mnie tam</a>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import EventActor from 'js/components/newsfeed/EventActor'

	export default {
		name: 'wnl-event-qna-answer-posted',
		props: ['event'],
		components: {
			'wnl-event-actor': EventActor
		}
	}
</script>
