<template>
	<wnl-structure-node-editor-form
		submit-label="Dodaj pojęcie"
		:on-save="onSave"
		:taxonomy-id="taxonomyId"
		:term="term"
		@parentChange="onParentChange"
	/>
</template>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlStructureNodeEditorForm from 'js/admin/components/structure/StructureNodeEditorForm';
import scrollToTaxonomyTermMixin from 'js/admin/mixins/scroll-to-taxonomy-term';

export default {
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapGetters('courseStructure', ['termById']),
		...mapState('courseStructure', ['selectedTerms']),
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
		WnlStructureNodeEditorForm
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', {
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
				this.scrollToTaxonomyTerm(parent);
			} else {
				this.selectTerms([]);
			}
		},

	},
	mixins: [
		scrollToTaxonomyTermMixin,
	],
};
</script>
