<template>
	<wnl-taxonomy-term-editor-form
		submit-label="Zapisz"
		:on-save="onSave"
		:taxonomy-id="taxonomyId"
		:term="term"
	/>
</template>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

import WnlTaxonomyTermEditorForm from 'js/admin/components/taxonomies/TaxonomyTermEditorForm';

export default {
	props: {
		taxonomyId: {
			type: String|Number,
			required: true,
		},
	},
	data() {
		return {
			term: {}
		};
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById']),
		...mapState('taxonomyTerms', ['selectedTerms']),
	},
	components: {
		WnlTaxonomyTermEditorForm
	},
	methods: {
		...mapActions('taxonomyTerms', {
			'updateTerm': 'update',
		}),
		async onSave(term) {
			return await this.updateTerm(term);
		},
		onSelectedTermsChange() {
			// TODO figure out multiple terms selected
			const term = this.termById(this.selectedTerms[0]);

			this.term = {
				description: term.description,
				id: term.id,
				parent: term.ancestors.slice(-1)[0],
				tag: term.tag
			};
		}
	},
	watch: {
		selectedTerms() {
			this.onSelectedTermsChange();
		}
	},
	mounted() {
		this.onSelectedTermsChange();
	},
};
</script>
