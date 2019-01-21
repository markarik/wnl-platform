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
import scrollToTaxonomyTermMixin from 'js/admin/mixins/scroll-to-taxonomy-term';

export default {
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById']),
		...mapState('taxonomyTerms', ['selectedTerms']),
		term() {
			if (this.selectedTerms.length) {
				return {parent: this.termById(this.selectedTerms[0])};
			}
			return {};
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
				this.scrollToTaxonomyTerm(parent);
			}
		},

	},
	mixins: [
		scrollToTaxonomyTermMixin,
	],
};
</script>
