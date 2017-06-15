<template>
	<div>
		<h3>Twoja kolekcja pytań i odpowiedzi</h3>
		<div class="notification margin top">Kolekcje już wkrótce będzie można łatwo przeszukiwać m.in. po przedmiotach i będą służyć jako wartościowe narzędzie do powtórek. Dlatego zachęcamy gorąco do zapisywania slajdów, pytań oraz pytań kontrolnych na bieżąco! <wnl-emoji :name="smiling"></wnl-emoji></div>
		<wnl-qna readOnly="true" :ids="qnaQuestionsIds"></wnl-qna>
	</div>
</template>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import Qna from 'js/components/qna/Qna'

	export default {
		name: 'QnaCollection',
		components: {
			'wnl-qna': Qna,
		},
		computed: {
			...mapGetters('collections', ['qnaQuestionsIds']),
		},
		methods: {
			...mapActions('qna', ['destroyQna'])
		},
		beforeDestroy() {
			this.destroyQna()
		}
	}
</script>
