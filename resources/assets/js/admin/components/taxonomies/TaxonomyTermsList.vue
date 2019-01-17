<template>
	<ul>
		<vue-draggable @end="onTermMove" :value="sortedTerms">
			<wnl-taxonomy-term-item
				v-for="term in sortedTerms"
				:term="term"
				:key="term.id"
				@moveTerm="onChildTermMove"
			/>
		</vue-draggable>
	</ul>
</template>

<script>
import {mapActions} from 'vuex';

import VueDraggable from 'vuedraggable';
import WnlTaxonomyTermItem from 'js/admin/components/taxonomies/TaxonomyTermItem';

export default {
	// Name is required to allow recursive rendering
	name: 'wnl-taxonomy-terms-list',
	components: {
		VueDraggable,
		WnlTaxonomyTermItem
	},
	props: {
		terms: {
			type: Array,
			required: true
		}
	},
	computed: {
		sortedTerms() {
			return this.terms
				.sort((termA, termB) => termA.orderNumber - termB.orderNumber);
		},
	},
	methods: {
		...mapActions('taxonomyTerms', ['dragTerm']),
		onTermMove({newIndex, oldIndex}) {
			this.dragTerm({
				newIndex, oldIndex, terms: this.sortedTerms
			});
		},
		onChildTermMove({term, direction}) {
			const oldIndex = this.sortedTerms.indexOf(term);
			const newIndex = Math.min(Math.max(oldIndex + direction, 0), this.sortedTerms.length - 1);

			this.dragTerm({
				terms: this.sortedTerms, oldIndex, newIndex
			});
		},
	}
};
</script>
