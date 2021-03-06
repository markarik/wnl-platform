<template>
	<div
		:class="{
			'content-classifier__editor': true,
			'wnl-is-loading': isLoading,
		}"
		tabindex="-1"
		@keydown="onKeyDown"
		@blur="$emit('blur', $event)"
	>
		<div v-if="allTaxonomyTerms.length===0" class="label is-uppercase margin bottom">Brak przypisanych pojęć</div>
		<div v-if="items.length > 0">
			<ul class="margin bottom">
				<li v-for="group in groupedTaxonomyTerms" :key="group.taxonomy.id">
					<ul class="content-classifier__editor__terms-group">
						<li
							v-for="term in group.terms"
							:key="term.id"
						>
							<wnl-taxonomy-term-with-ancestors
								:term="term"
								:ancestors="term.ancestors"
								:class="{
									'content-classifier__editor__term': true,
									'has-parent': term.parent_id !== null,
								}"
								is-bordered
							>
								<span
									slot="left"
									class="icon is-small margin right clickable"
									title="Odznacz"
									@click="onDetachTaxonomyTerm(term)"
								>
									<i class="fa fa-close" />
								</span>
								<span
									v-if="allItemsCount > 1"
									slot="right"
									:class="{
										'margin': true,
										'left': true,
										'tag': true,
										'strong': hasAllItemsAttached(term),
										'is-white': !hasAllItemsAttached(term),
										'clickable': !hasAllItemsAttached(term),
									}"
									:title="!hasAllItemsAttached(term) && 'Dodaj do wszystkich'"
									@click="!hasAllItemsAttached(term) && onAttachTaxonomyTerm(term)"
								>
									<span v-if="!hasAllItemsAttached(term)" class="icon is-small"><i class="fa fa-plus" /></span>
									<span>{{term.itemsCount}}/{{allItemsCount}}</span>
								</span>
							</wnl-taxonomy-term-with-ancestors>

						</li>
					</ul>
				</li>
			</ul>

			<wnl-content-classifier-editor-recent-terms
				:last-used-term="lastUsedTerm"
				:last-used-terms-set="lastUsedTermsSet"
				:items="items"
				@attachTaxonomyTerm="onAttachTaxonomyTerm"
			/>

			<div class="field">
				<label class="label small is-uppercase"><strong>Przypisz pojęcie</strong></label>
				<wnl-taxonomy-term-selector
					:is-down="false"
					:is-focused="isTaxonomyTermAutocompleteFocused"
					@blur="onTaxonomyTermAutocompleteBlur"
					@change="onAttachTaxonomyTerm"
				/>
			</div>
		</div>
		<div v-else class="notification is-info">
			<span class="icon"><i class="fa fa-info-circle" /></span>
			<span>Najpierw wybierz treść do klasyfikacji</span>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.content-classifier__editor

		&__terms-group
			display: flex
			flex-wrap: wrap

		.content-classifier__editor__term
			display: flex
			margin-bottom: $margin-tiny
			margin-top: $margin-tiny

			/deep/ .taxonomy-term__content
				flex-grow: 1

	.icon.is-small .fa
		font-size: $font-size-minus-2
</style>

<script>
import axios from 'axios';
import { mapActions, mapGetters } from 'vuex';
import { uniqBy, cloneDeep } from 'lodash';

import { getApiUrl } from 'js/utils/env';
import { ALERT_TYPES } from 'js/consts/alert';

import WnlContentClassifierEditorRecentTerms from 'js/components/global/contentClassifier/ContentClassifierEditorRecentTerms';
import WnlTaxonomyTermWithAncestors from 'js/components/global/taxonomies/TaxonomyTermWithAncestors';
import WnlTaxonomyTermSelector from 'js/components/global/taxonomies/TaxonomyTermSelector';
import { CONTENT_TYPES } from 'js/consts/contentClassifier';
import contentClassifierStore from 'js/services/contentClassifierStore';
import { CONTENT_CLASSIFIER_STORE_KEYS } from 'js/services/contentClassifierStore';
import { scrollToElement } from 'js/utils/animations';

export default {
	components: {
		WnlTaxonomyTermSelector,
		WnlTaxonomyTermWithAncestors,
		WnlContentClassifierEditorRecentTerms,
	},
	props: {
		items: {
			type: Array,
			required: true,
		},
		isFocused: {
			type: Boolean,
			default: false
		},
	},
	data() {
		return {
			isLoading: false,
			isTaxonomyTermAutocompleteFocused: false,
			taxonomyId: null,
			triggerAttachLastUsedTerm: false,
			triggerAttachLastUsedTermsSet: false,
			lastUsedTerm: contentClassifierStore.get(CONTENT_CLASSIFIER_STORE_KEYS.LAST_TERM),
			lastUsedTermsSet: contentClassifierStore.get(CONTENT_CLASSIFIER_STORE_KEYS.ALL_TERMS, []),
		};
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById', 'getAncestorNodesById']),
		...mapGetters('taxonomies', ['taxonomyById']),
		allItemsCount() {
			return this.items.length;
		},
		allTaxonomyTerms() {
			return cloneDeep(uniqBy([].concat(...this.items.map(item => item.taxonomyTerms)), 'id'));
		},
		groupedTaxonomyTerms() {
			const groupedTerms = this.allTaxonomyTerms.reduce(
				(collector, term) => {
					if (!collector[term.taxonomy.id]) {
						collector[term.taxonomy.id] = {
							taxonomy: term.taxonomy,
							terms: [],
						};
					}

					term.itemsCount = this.getItemsCountByTermId(term.id);
					collector[term.taxonomy.id].terms.push(term);

					return collector;
				},
				{}
			);

			Object.keys(groupedTerms)
				.forEach(taxonomyId => groupedTerms[taxonomyId].terms.sort((a, b) => b.itemsCount - a.itemsCount));

			return groupedTerms;
		},
	},
	watch: {
		async isFocused() {
			if (this.isFocused) {
				scrollToElement(this.$el);
				this.$el.focus();
			} else {
				this.$el.blur();
			}
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		getItemsCountByTermId(termId) {
			return this.items.filter(item => item.taxonomyTerms.find(term => term.id === termId)).length;
		},
		getItemsByType(contentType) {
			return this.items.filter(item => item.type === contentType);
		},
		hasAllItemsAttached(term) {
			return term.itemsCount === this.allItemsCount;
		},
		async onDetachTaxonomyTerm(term) {
			this.isLoading = true;
			try {
				await axios.post(getApiUrl(`taxonomy_terms/${term.id}/detach`), {
					annotations: this.getItemsByType(CONTENT_TYPES.ANNOTATION).map(item => item.id),
					flashcards: this.getItemsByType(CONTENT_TYPES.FLASHCARD).map(item => item.id),
					quiz_questions: this.getItemsByType(CONTENT_TYPES.QUIZ_QUESTION).map(item => item.id),
					slides: this.getItemsByType(CONTENT_TYPES.SLIDE).map(item => item.id),
				});

				this.$emit('taxonomyTermDetached', term);
				contentClassifierStore.set(CONTENT_CLASSIFIER_STORE_KEYS.ALL_TERMS, this.allTaxonomyTerms);
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
		async onAttachTaxonomyTerm(term, taxonomyId) {
			this.isLoading = true;
			try {
				await axios.post(getApiUrl(`taxonomy_terms/${term.id}/attach`), {
					annotations: this.getItemsByType(CONTENT_TYPES.ANNOTATION).map(item => item.id),
					flashcards: this.getItemsByType(CONTENT_TYPES.FLASHCARD).map(item => item.id),
					quiz_questions: this.getItemsByType(CONTENT_TYPES.QUIZ_QUESTION).map(item => item.id),
					slides: this.getItemsByType(CONTENT_TYPES.SLIDE).map(item => item.id),
				});

				const termToAdd = {
					...term,
					taxonomy: term.taxonomy || this.taxonomyById(taxonomyId),
					ancestors: term.ancestors || this.getAncestorNodesById(term.id),
				};
				this.$emit('taxonomyTermAttached', termToAdd);
				contentClassifierStore.set(CONTENT_CLASSIFIER_STORE_KEYS.LAST_TERM, termToAdd);
				contentClassifierStore.set(CONTENT_CLASSIFIER_STORE_KEYS.ALL_TERMS, this.allTaxonomyTerms);
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Nie udało się zapisać nowej klasyfikacji. Spróbuj ponownie.',
					type: ALERT_TYPES.ERROR
				});
			} finally {
				this.isLoading = false;
				this.isTaxonomyTermAutocompleteFocused = true;
			}
		},
		async onTaxonomyTermAutocompleteBlur() {
			this.isTaxonomyTermAutocompleteFocused = false;
		},
		onKeyDown(event) {
			if (!this.$shortcutKeyIsEditable(event.target)) {
				switch (event.key) {
				case 't':
					// Disable global shortcut
					event.stopImmediatePropagation();
					this.isTaxonomyTermAutocompleteFocused = true;
					break;

				case 'r':
					if (this.lastUsedTerm) {
						this.onAttachTaxonomyTerm(this.lastUsedTerm);
					}
					break;
				case 'R':
					if (this.lastUsedTermsSet) {
						this.lastUsedTermsSet.forEach(term => this.onAttachTaxonomyTerm(term));
					}
					break;
				}
			}
		},
	},
};
</script>
