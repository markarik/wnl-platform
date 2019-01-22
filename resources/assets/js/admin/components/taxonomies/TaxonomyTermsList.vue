<template>
	<ul>
		<vue-draggable @end="onTermDrag" :value="terms">
			<wnl-taxonomy-term-item
				v-for="term in terms"
				:term="term"
				:key="term.id"
				@moveTerm="onChildTermArrowMove"
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
		...mapActions(['addAutoDismissableAlert']),
		submitMove(args) {
			try {
				this.moveTerm({...args});
				this.addAutoDismissableAlert({
					type: 'success',
					text: 'Zapisano!'
				});
			} catch (e) {
				this.addAutoDismissableAlert({
					type: 'error',
					text: 'Nie udało się zapisać zmiany. Odśwież stronę i spróbuj ponownie. Być może Twoje drzewo jest nieaktulane.'
				});
				$wnl.logger.error(e);
				throw (e);
			}
		},
		onTermDrag({newIndex, oldIndex}) {
			try {
				this.submitMove({newIndex, oldIndex, terms: this.terms});
			} catch (e) {
				this.submitMove({newIndex: oldIndex, oldIndex: newIndex, terms: this.terms});
			}
		},
		onChildTermArrowMove({term, direction}) {
			const oldIndex = this.terms.indexOf(term);
			const newIndex = Math.min(Math.max(oldIndex + direction, 0), this.terms.length - 1);
			try {
				this.submitMove({newIndex, oldIndex, terms: this.terms});
			} catch (e) {
				this.submitMove({});
			}
		},
	}
};
</script>
