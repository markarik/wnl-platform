<template>
	<div>
		<h3>Twoja kolekcja pytań i odpowiedzi</h3>
		<div class="notification margin top"><strong>Kolekcje już wkrótce będzie można łatwo przeszukiwać m.in.&nbsp;po&nbsp;przedmiotach oraz słowach kluczowych, więc będą służyć jako wartościowe narzędzie do powtórek!</strong> Dlatego zachęcamy Cię gorąco do zapisywania w&nbsp;Twoich Kolekcjach slajdów, pytań oraz pytań kontrolnych na bieżąco!</div>
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
