<template>
	<div>
		<h3>Twoja kolekcja pytań i odpowiedzi</h3>
		<wnl-qna readOnly="true"></wnl-qna>
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
		methods: {
			...mapActions('qna', ['destroyQna'])
		},
		beforeDestroy() {
			this.destroyQna()
		}
	}
</script>
