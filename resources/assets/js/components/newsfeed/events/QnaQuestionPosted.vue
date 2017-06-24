<template lang="html">
	<!--<div>-->
	<!--<strong>Nowa odpowiedź na pytanie</strong>-->
	<!--"{{ event.objects.text }}"-->
	<!--<strong>od</strong>-->
	<!--<wnl-avatar-->
	<!--:fullName="event.actors.full_name"-->
	<!--size="small"-->
	<!--&gt;</wnl-avatar>-->
	<!--<a>{{ event.actors.full_name }}</a>-->
	<!--"{{ event.subject.text }}" <a :href="event.referer" target="_blank">jedziesz szwagier</a>-->
	<!--</div>-->
</template>

<style lang="sass">
</style>

<script>
	export default {
		name: 'wnl-event-qna-answer-posted',
		props: ['event'],
	}
</script>
