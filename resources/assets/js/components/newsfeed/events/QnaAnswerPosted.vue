<template lang="html">
	<div>
		Nowa odpowiedź na pytanie "{{ event.objects.text }}"
		od <a>{{ event.actors.full_name }}</a>

	</div>
</template>

<style lang="sass">
</style>

<script>
	export default {
		name: 'wnl-event-qna-answer-posted',
		props: ['event'],
		mounted() {
			console.log(this.event)
		}
	}
</script>
