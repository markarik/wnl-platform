<template>
	<div class="collections-qna">
		<wnl-qna
			:sorting-enabled="true"
			title="Zapisane pytania i odpowiedzi"
			:read-only="true"
		></wnl-qna>
		<div v-if="isZeroState" class="notification has-text-centered margin top">
			W temacie <span class="metadata">{{rootCategoryName}} <span class="icon is-small"><i class="fa fa-angle-right"></i></span> {{categoryName}}</span> nie ma jeszcze zapisanych pytań i odpowiedzi. Możesz łatwo to zmienić klikając na <span class="icon is-small"><i class="fa fa-star-o"></i></span> <span class="metadata">ZAPISZ</span> przy wybranym wątku!
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.collections-qna
		padding: $margin-base 0
</style>

<script>
import { mapGetters } from 'vuex';

import Qna from 'js/components/qna/Qna';

export default {
	name: 'QnaCollection',
	components: {
		'wnl-qna': Qna,
	},
	props: ['rootCategoryName', 'categoryName'],
	computed: {
		...mapGetters('qna', ['loading', 'questions']),
		isZeroState() {
			return !this.loading && Object.keys(this.questions).length === 0;
		},
	},
};
</script>
