<template lang="html">
	<div>
		<wnl-avatar :fullName="event.actors.full_name" size="small"/>
		<a>{{ event.actors.full_name }}</a>
		odpowiedział/-a na pytanie <br>
		"{{ event.subject.text }}"
	</div>
</template>

<style lang="sass">
</style>

<script>
	export default {
		name: 'wnl-event-qna-answer-posted',
		props: ['event'],
	}
</script>
