<template>
	<div class="current-term margin bottom">
		<p class="margin right"><slot></slot></p>
		<wnl-taxonomy-term-with-ancestors
			:ancestors="getAncestorsById(term.id)"
			:term="term"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.current-term
		align-items: center
		border-bottom: $border-light-gray
		display: flex
		padding-bottom: $margin-base
		text-align: left
</style>

<script>
import {mapGetters} from 'vuex';

import WnlTaxonomyTermWithAncestors from 'js/components/global/taxonomies/TaxonomyTermWithAncestors';

export default {
	components: {
		WnlTaxonomyTermWithAncestors
	},
	props: {
		term: {
			type: Object,
			required: true,
		}
	},
	computed: {
		...mapGetters('taxonomyTerms', ['getAncestorsById']),
	},
};
</script>
