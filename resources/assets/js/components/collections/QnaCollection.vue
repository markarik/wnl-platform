<template>
	<div class="collections-qna">
		<wnl-qna title="Zapisane pytania i odpowiedzi" readOnly="true"></wnl-qna>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.collections-qna
		padding: $margin-base 0
</style>

<script>
	import Qna from 'js/components/qna/Qna'

	export default {
		name: 'QnaCollection',
		components: {
			'wnl-qna': Qna,
		},
	}
</script>
