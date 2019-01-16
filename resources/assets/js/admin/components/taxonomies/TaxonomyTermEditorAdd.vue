<template>
	<wnl-taxonomy-term-editor-form
		submit-label="Dodaj pojęcie"
		:on-save="onSave"
		:taxonomy-id="taxonomyId"
		:term="term"
	/>
</template>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

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
			term: {
				description: '',
				id: null,
				tag: null,
				parent: null,
			}
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
			'createTerm': 'create',
		}),
		...mapActions('tags', {
			fetchAllTags: 'fetchAll'
		}),
		async onSave(term) {
			await this.createTerm(term);
		},
		initializeParent(selectedTerms) {
			if (selectedTerms.length) {
				this.term = Object.assign({}, this.term, {parent: this.termById(selectedTerms[0])});
			}
		},
	},
	mounted() {
		this.fetchAllTags();
		this.initializeParent(this.selectedTerms);
	},
	watch: {
		selectedTerms(selectedTerms) {
			this.initializeParent(selectedTerms);
		},
	},
};
</script>
