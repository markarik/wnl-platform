<template>
	<div v-if="term">
		<wnl-taxonomy-term-editor-current-term :term="term">
			Edytujesz pojęcie:
		</wnl-taxonomy-term-editor-current-term>
		<wnl-taxonomy-term-editor-form
			submit-label="Zapisz"
			:on-save="onSave"
			:taxonomy-id="taxonomyId"
			:term="term"
		/>
	</div>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz pojęcie
	</div>
</template>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

import WnlTaxonomyTermEditorForm from 'js/admin/components/taxonomies/TaxonomyTermEditorForm';
import WnlTaxonomyTermEditorCurrentTerm from 'js/admin/components/taxonomies/TaxonomyTermEditorCurrentTerm';

export default {
	components: {
		WnlTaxonomyTermEditorForm,
		WnlTaxonomyTermEditorCurrentTerm,
	},
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapGetters('taxonomyTerms', {
			termById: 'nodeById',
			getAncestorsById: 'getAncestorsById',
		}),
		...mapState('taxonomyTerms', {selectedTerms: 'selectedNodes'}),
		term() {
			if (this.selectedTerms.length === 0) {
				return null;
			}

			// TODO figure out multiple terms selected
			const term = this.termById(this.selectedTerms[0]);

			return {
				...term,
				parent: this.getAncestorsById(term.id).slice(-1)[0],
			};
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', {
			'expandTerm': 'expand',
			'updateTerm': 'update',
		}),
		async onSave(term) {
			try {
				await this.updateTerm(term);

				if (term.parent_id) {
					this.expandTerm(term.parent_id);
				}

				this.addAutoDismissableAlert({
					text: 'Zapisano pojęcie!',
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
	},
};
</script>
