<template>
	<div class="content-classifier__panel-editor">
		<h4 class="title is-4 margin bottom">Zarządzaj</h4>
		<label class="label is-uppercase"><strong>Pojęcia</strong></label>
		<ul class="margin bottom">
			<li v-for="group in groupedTaxonomyTerms" :key="group.taxonomy.id" class="margin bottom">
				<div class="content-classifier__panel-editor__taxonomy">
					{{group.taxonomy.name}}
				</div>
				<ul>
					<li v-for="term in group.terms" :key="term.id">
						<strong>{{term.tag.name}}</strong> ({{getItemsCountByTermId(term.id)}}/{{allItemsCount}})
						<button
							class="button is-danger is-small"
							@click="onDetachTaxonomyTermable(term)"
						>
							<i class="fa fa-trash"></i>
						</button>
					</li>
				</ul>
			</li>
		</ul>
		<div class="field">
			<label class="label is-uppercase"><strong>Dodaj pojęcie</strong></label>
			<wnl-select
				:options="taxonomiesOptions"
				v-model="taxonomyId"
				@input="onTaxonomyChange"
			/>
			<wnl-taxonomy-term-autocomplete
				placeholder="Wyszukaj pojęcie"
				@change="onTermAdded"
			/>
		</div>

	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-classifier
		&__panel-editor
			flex: 50%

			&__taxonomy
				text-transform: uppercase
</style>

<script>
import axios from 'axios';
import {mapActions, mapGetters, mapState} from 'vuex';
import {uniqBy} from 'lodash';

import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

import WnlSelect from 'js/admin/components/forms/Select';
import WnlTaxonomyTermAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermAutocomplete';

export default {
	components: {
		WnlTaxonomyTermAutocomplete,
		WnlSelect,
	},
	data() {
		return {
			taxonomyId: null,
		};
	},
	props: {
		filteredContent: {
			type: Object,
			required: true,
		}
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById']),
		...mapGetters('taxonomies', ['taxonomyById']),
		...mapState('taxonomies', ['taxonomies']),
		flattenItems() {
			return [].concat(...Object.values(this.filteredContent));
		},
		allItemsCount() {
			return this.flattenItems.length;
		},
		groupedTaxonomyTerms() {
			const taxonomyTerms = uniqBy([].concat(...this.flattenItems.map(item => item.taxonomyTerms)), 'id');
			const groupedTerms = {};

			taxonomyTerms.forEach(term => {
				if (!groupedTerms[term.taxonomy.id]) {
					groupedTerms[term.taxonomy.id] = {
						taxonomy: term.taxonomy,
						terms: [],
					};
				}

				groupedTerms[term.taxonomy.id].terms.push(term);
			});

			return groupedTerms;
		},
		taxonomiesOptions() {
			if (!this.taxonomies) {
				return [];
			}

			return this.taxonomies.map(taxonomy => ({value: taxonomy.id, text: taxonomy.name}));
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', ['fetchTermsByTaxonomy']),
		...mapActions('taxonomies', {
			fetchTaxonomies: 'fetchAll',
		}),
		getItemsCountByTermId(termId) {
			return this.flattenItems.filter(item => item.taxonomyTerms.find(term => term.id === termId)).length;
		},
		async onDetachTaxonomyTermable(term) {
			try {
				await axios.post(getApiUrl(`taxonomy_terms/${term.id}/detach`), {
					annotations: this.filteredContent.annotations.map(item => item.id),
					flashcards: this.filteredContent.flashcards.map(item => item.id),
					quiz_questions: this.filteredContent.quizQuestions.map(item => item.id),
					slides: this.filteredContent.slides.map(item => item.id),
				});
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Nie udało się usunąć klasyfikacji. Spróbuj ponownie.',
					type: ALERT_TYPES.ERROR
				});
			}
		},
		async onTermAdded(term) {
			try {
				await axios.post(getApiUrl(`taxonomy_terms/${term.id}/attach`), {
					annotations: this.filteredContent.annotations.map(item => item.id),
					flashcards: this.filteredContent.flashcards.map(item => item.id),
					quiz_questions: this.filteredContent.quizQuestions.map(item => item.id),
					slides: this.filteredContent.slides.map(item => item.id),
				});

				this.$emit('onTermAdded', term, this.taxonomyById(this.taxonomyId));
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Nie udało się zapisać nowej klasyfikacji. Spróbuj ponownie.',
					type: ALERT_TYPES.ERROR
				});
			}
		},
		async onTaxonomyChange(taxonomyId) {
			try {
				await this.fetchTermsByTaxonomy(taxonomyId);
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Coś poszło nie tak przy pobieraniu struktury Taksonomii',
					type: ALERT_TYPES.ERROR
				});
			}
		}
	},
	async mounted() {
		try {
			await this.fetchTaxonomies();
		} catch (error) {
			$wnl.logger.capture(error);
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak przy pobieraniu listy Taksonomii',
				type: ALERT_TYPES.ERROR
			});
		}
	},
};
</script>
