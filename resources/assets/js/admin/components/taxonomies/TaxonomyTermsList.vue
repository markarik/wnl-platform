<template>
	<ul>
		<vue-draggable @end="onTermDrag" :value="terms">
			<wnl-taxonomy-term-item
				v-for="term in terms"
				:term="term"
				:loading="isSaving"
				:key="term.id"
				:class="[isSaving && 'taxonomy-term-item--disabled']"
				@moveTerm="onChildTermArrowMove"
			/>
		</vue-draggable>
	</ul>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.taxonomy-term-item--disabled
		pointer-events: none
		color: $color-gray-dimmed
</style>

<script>
import {mapActions, mapState} from 'vuex';

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
	computed: {
		...mapState('taxonomyTerms', ['isSaving'])
	},
	methods: {
		...mapActions('taxonomyTerms', ['moveTerm', 'reorderSiblings']),
		...mapActions(['addAutoDismissableAlert']),
		async submitMove(args) {
			try {
				await this.moveTerm({...args});
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
		async onTermDrag({newIndex, oldIndex}) {
			const direction = newIndex - oldIndex;
			const term = this.terms[oldIndex];
			try {
				await this.submitMove({direction, term});
			} catch (e) {
				await this.reorderSiblings({direction: oldIndex - newIndex, term});
			}
		},
		async onChildTermArrowMove({term, direction}) {
			try {
				await this.submitMove({direction, term});
			} catch (e) {
				await this.reorderSiblings({direction: -direction, term});
			}
		},
	}
};
</script>
