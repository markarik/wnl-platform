<template>
	<ul>
		<vue-draggable @end="onTermMove" :value="terms">
			<wnl-taxonomy-term-item
				v-for="term in terms"
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
	methods: {
		...mapActions('taxonomyTerms', ['moveTerm']),
		onTermMove({newIndex, oldIndex}) {
			this.moveTerm({
				newIndex, oldIndex, terms: this.terms
			});
		},
		onChildTermMove({term, direction}) {
			const oldIndex = this.terms.indexOf(term);
			const newIndex = Math.min(Math.max(oldIndex + direction, 0), this.terms.length - 1);

			this.moveTerm({
				terms: this.terms, oldIndex, newIndex
			});
		},
	}
};
</script>
