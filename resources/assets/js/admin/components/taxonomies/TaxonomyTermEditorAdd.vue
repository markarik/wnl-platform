<template>
	<wnl-taxonomy-term-editor-form
		submit-label="Dodaj pojęcie"
		:on-save="onSave"
		:taxonomy-id="taxonomyId"
		:term="term"
		@parentChange="onParentChange"
	/>
</template>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlTaxonomyTermEditorForm from 'js/admin/components/taxonomies/TaxonomyTermEditorForm';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

export default {
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapGetters('taxonomyTerms', {termById: 'nodeById'}),
		...mapState('taxonomyTerms', {selectedTerms: 'selectedNodes'}),
		term() {
			if (this.selectedTerms.length === 0) {
				return null;
			}

			return {
				parent: this.termById(this.selectedTerms[0])
			};
		}
	},
	components: {
		WnlTaxonomyTermEditorForm
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', {
			'createTerm': 'create',
			'expandTerm': 'expand',
			'selectTerms': 'select',
		}),
		async onSave(term) {
			try {
				await this.createTerm(term);

				if (term.parent_id) {
					this.expandTerm(term.parent_id);
				}

				this.addAutoDismissableAlert({
					text: 'Dodano pojęcie!',
					type: 'success'
				});
			} catch (error) {
				$wnl.logger.capture(error);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error',
				});
			}
		},
		onParentChange(parent) {
			if (parent) {
				this.selectTerms([parent.id]);
				this.expandTerm(parent.id);
				this.scrollToNode(parent);
			} else {
				this.selectTerms([]);
			}
		},

	},
	mixins: [
		scrollToNodeMixin,
	],
};
</script>
