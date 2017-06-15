<template>
	<div>
		<h3>Twoja kolekcja pytań i odpowiedzi</h3>
		<wnl-qna :ids="qnaQuestionsIds"></wnl-qna>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapGetters} from 'vuex'

	import Qna from 'js/components/qna/Qna'

	export default {
		name: 'QnaCollection',
		components: {
			'wnl-qna': Qna,
		},
		computed: {
			...mapGetters('collections', ['qnaQuestionsIds']),
		}
	}
</script>
