<template>
	<ul>
		<vue-draggable
			:options="{handle: '.taxonomy-term-item__action--drag'}"
			@end="onTermDrag"
		>
			<wnl-taxonomy-term-item
				v-for="term in terms"
				:key="term.id"
				:term="term"
			/>
		</vue-draggable>
	</ul>
</template>

<script>
import { mapActions } from 'vuex';

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
		...mapActions('taxonomyTerms', {
			moveTerm: 'moveNode',
			reorderSiblings: 'reorderSiblings',
		}),
		...mapActions(['addAutoDismissableAlert']),
		async submitMove({ direction, ...args }) {
			if (direction === 0) {
				return;
			}

			try {
				await this.moveTerm({ direction, ...args });
				this.addAutoDismissableAlert({
					type: 'success',
					text: 'Zapisano!'
				});
			} catch (e) {
				this.addAutoDismissableAlert({
					type: 'error',
					text: 'Nie udało się zapisać zmiany. Odśwież stronę i spróbuj ponownie. Być może Twoje drzewo jest nieaktualne.'
				});
				$wnl.logger.error(e);
				throw (e);
			}
		},
		async onTermDrag({ newIndex, oldIndex }) {
			const direction = newIndex - oldIndex;
			const term = this.terms[oldIndex];
			try {
				await this.submitMove({ direction, node: term });
			} catch (e) {
				await this.reorderSiblings({ direction: oldIndex - newIndex, node: term });
			}
		},
	}
};
</script>
