<template>
	<wnl-taxonomy-term-editor-form
		v-if="term"
		submit-label="Zapisz"
		:on-save="onSave"
		:taxonomy-id="taxonomyId"
		:term="term"
	/>
	<p v-else>Najpierw wybierz pojęcia</p>
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
			term: null,
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
			if (this.selectedTerms.length === 0) {
				this.term = null;
				return;
			}

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
