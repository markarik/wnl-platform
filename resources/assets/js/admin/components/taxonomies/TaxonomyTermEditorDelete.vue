<template>
	<div
		v-if="term"
		class="has-text-centered"
	>
		<wnl-taxonomy-term-editor-current-term :term="term">
			Usuwasz pojęcie:
		</wnl-taxonomy-term-editor-current-term>
		<p class="margin bottom">
			Czy na pewno chcesz usunąć pojęcie <em><strong>{{term.tag.name}}</strong></em> wraz z potomkami?
		</p>
		<button
			class="button is-danger"
			:disabled="isSaving"
			@click="onDelete"
		>
			<span class="icon is-small"><i class="fa fa-trash" aria-hidden="true" /></span>
			<span>Usuń pojęcie</span>
		</button>
	</div>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle" />
		</span>
		Najpierw wybierz pojęcie
	</div>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex';

import WnlTaxonomyTermEditorCurrentTerm from 'js/admin/components/taxonomies/TaxonomyTermEditorCurrentTerm';

export default {
	components: {
		WnlTaxonomyTermEditorCurrentTerm,
	},
	computed: {
		...mapGetters('taxonomyTerms', { termById: 'nodeById' }),
		...mapState('taxonomyTerms', {
			isSaving: 'isSaving',
			selectedTerms: 'selectedNodes'
		}),
		term() {
			if (this.selectedTerms.length === 0) {
				return null;
			}

			// TODO figure out multiple terms selected
			return this.termById(this.selectedTerms[0]);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', {
			'deleteTerm': 'delete',
			'selectTerms': 'select'
		}),
		async onDelete() {
			try {
				await this.deleteTerm(this.term);
				this.selectTerms([]);

				this.addAutoDismissableAlert({
					text: 'Usunięto pojęcie!',
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
