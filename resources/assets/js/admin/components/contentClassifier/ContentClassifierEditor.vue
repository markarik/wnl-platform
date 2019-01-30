<template>
	<div :class="{
		'content-classifier__panel-editor': true,
		'is-loading': isLoading,
	}">
		<h4 class="title is-4 margin bottom">Przypisane pojęcia</h4>
		<ul class="margin bottom">
			<li v-for="group in groupedTaxonomyTerms" :key="group.taxonomy.id" class="margin bottom">
				<div class="content-classifier__panel-editor__taxonomy">
					{{group.taxonomy.name}}
				</div>
				<ul>
					<li
						v-for="term in group.terms"
						:key="term.id"
						:class="[{
							'content-classifier__panel-editor__term': true,
							'content-classifier__panel-editor__term--has-parent': term.parent_id !== null,
							'content-classifier__panel-editor__term--is-partial': term.itemsCount < allItemsCount,
						}]"
					>
						<span
							class="icon is-small margin right clickable"
							title="Odznacz"
							@click="onDetachTaxonomyTerm(term)"
						>
							<i class="fa fa-close"></i>
						</span>
						<wnl-taxonomy-term-with-ancestors
							:term="term"
							:ancestors="term.ancestors"
							class="content-classifier__panel-editor__term__name"
						/>
						<span
							class="content-classifier__panel-editor__term__counter margin left"
							v-if="term.itemsCount < allItemsCount"
							@click="onAttachTaxonomyTerm(term)"
							title="Dodaj do wszystkich"
						>
							<span class="icon is-small"><i class="fa fa-plus"></i></span>
							<span>{{term.itemsCount}}/{{allItemsCount}}</span>
						</span>
					</li>
				</ul>
			</li>
		</ul>
		<div class="field">
			<label class="label is-uppercase"><strong>Przypisz pojęcie</strong></label>
			<div class="content-classifier__panel-editor__term-select">
				<wnl-select
					:options="taxonomiesOptions"
					v-model="taxonomyId"
					@input="onTaxonomyChange"
				/>
				<wnl-taxonomy-term-autocomplete
					placeholder="Zacznij pisać, aby wyszukać pojęcie"
					@change="onAttachTaxonomyTerm"
					class="margin left content-classifier__panel-editor__term-select__autocomplete"
				/>
			</div>
		</div>

	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-classifier
		&__panel-editor
			flex: 50%

			&__term
				align-items: center
				border-bottom: $border-light-gray
				display: flex
				padding: $margin-medium 0

				&--has-parent &__name
					font-size: $font-size-minus-1

				&--is-partial
					opacity: .6

				&__name
					flex-grow: 1

				&__counter
					cursor: pointer
					font-size: $font-size-minus-1

			&__taxonomy
				text-transform: uppercase
			&__term-select
				display: flex
				&__autocomplete
					flex-grow: 1
</style>

<script>
import axios from 'axios';
import {mapActions, mapGetters, mapState} from 'vuex';
import {uniqBy} from 'lodash';

import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

import WnlSelect from 'js/admin/components/forms/Select';
import WnlTaxonomyTermAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermAutocomplete';
import WnlTaxonomyTermWithAncestors from 'js/admin/components/taxonomies/TaxonomyTermWithAncestors';

export default {
	components: {
		WnlSelect,
		WnlTaxonomyTermAutocomplete,
		WnlTaxonomyTermWithAncestors,
	},
	data() {
		return {
			taxonomyId: null,
			isLoading: false,
		};
	},
	props: {
		filteredContent: {
			type: Array,
			required: true,
		}
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById', 'getAncestorsById']),
		...mapGetters('taxonomies', ['taxonomyById']),
		...mapState('taxonomies', ['taxonomies']),
		allItemsCount() {
			return this.filteredContent.length;
		},
		groupedTaxonomyTerms() {
			const taxonomyTerms = uniqBy([].concat(...this.filteredContent.map(item => item.taxonomyTerms)), 'id');
			const groupedTerms = {};

			taxonomyTerms.forEach(term => {
				if (!groupedTerms[term.taxonomy.id]) {
					groupedTerms[term.taxonomy.id] = {
						taxonomy: term.taxonomy,
						terms: [],
					};
				}

				term.itemsCount = this.getItemsCountByTermId(term.id);
				groupedTerms[term.taxonomy.id].terms.push(term);
			});

			Object.keys(groupedTerms).forEach(taxonomyId => groupedTerms[taxonomyId].terms.sort((a, b) => b.itemsCount - a.itemsCount));

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
			return this.filteredContent.filter(item => item.taxonomyTerms.find(term => term.id === termId)).length;
		},
		getItemsByType(contentType) {
			return this.filteredContent.filter(item => item.type === contentType);
		},
		async onDetachTaxonomyTerm(term) {
			this.isLoading = true;
			try {
				await axios.post(getApiUrl(`taxonomy_terms/${term.id}/detach`), {
					annotations: this.getItemsByType('annotations').map(item => item.id),
					flashcards: this.getItemsByType('flashcards').map(item => item.id),
					quiz_questions: this.getItemsByType('quizQuestions').map(item => item.id),
					slides: this.getItemsByType('slides').map(item => item.id),
				});

				this.$emit('onTaxonomyTermDetached', term);
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Nie udało się usunąć klasyfikacji. Spróbuj ponownie.',
					type: ALERT_TYPES.ERROR
				});
			} finally {
				this.isLoading = false;
			}
		},
		async onAttachTaxonomyTerm(term) {
			this.isLoading = true;
			try {
				await axios.post(getApiUrl(`taxonomy_terms/${term.id}/attach`), {
					annotations: this.getItemsByType('annotations').map(item => item.id),
					flashcards: this.getItemsByType('flashcards').map(item => item.id),
					quiz_questions: this.getItemsByType('quizQuestions').map(item => item.id),
					slides: this.getItemsByType('slides').map(item => item.id),
				});

				const termToAdd = {
					...term,
					taxonomy: term.taxonomy || this.taxonomyById(this.taxonomyId),
					ancestors: term.ancestors || this.getAncestorsById(term.id),
				};
				this.$emit('onTaxonomyTermAttached', termToAdd);
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Nie udało się zapisać nowej klasyfikacji. Spróbuj ponownie.',
					type: ALERT_TYPES.ERROR
				});
			} finally {
				this.isLoading = false;
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
