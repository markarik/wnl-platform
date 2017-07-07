<template>
	<div class="collections-qna">
		<!-- <div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item metadata">Twoje zapisane pytania i odpowiedzi</div>
			</div>
		</div> -->
		<wnl-qna title="Zapisane pytania i odpowiedzi" readOnly="true"></wnl-qna>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.collections-qna
		padding: $margin-base 0
</style>

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
