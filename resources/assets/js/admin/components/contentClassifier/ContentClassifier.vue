<template>
	<div class="content-classifier">
		<h3 class="title is-3">Klasyfikacja treści</h3>
		<div class="tabs">
			<ul>
				<li
					:class="{'is-active': activeTab === TABS.BY_CLASSIFICATION}"
					@click="activeTab=TABS.BY_CLASSIFICATION"
				>
					<a>Po klasyfikacji</a>
				</li>
				<li
					:class="{'is-active': activeTab === TABS.BY_ID}"
					@click="activeTab=TABS.BY_ID"
				>
					<a>Po id</a>
				</li>
			</ul>
		</div>

		<wnl-content-classifier-filter-by-classification
			v-show="activeTab === TABS.BY_CLASSIFICATION"
			:content-types="contentTypes"
			@search="onSearchByTag"
		/>

		<wnl-content-classifier-filter-by-ids
			v-show="activeTab === TABS.BY_ID"
			:content-types="contentTypes"
			@search="onSearchById"
		/>

		<div class="content-classifier__panels" v-if="filteredContent !== null">
			<div class="content-classifier__panel-results">
				<div class="content-classifier__panel-results__header">
					<h4 class="title is-4 margin bottom">Wyniki wyszukiwania</h4>
					<a @click="selectAll">Zaznacz wszystkie</a>
				</div>
				<wnl-text-loader v-if="isLoading" />
				<div v-else-if="filteredContent.length">
					<div
						v-for="(meta, contentType) in contentTypes"
						:key="contentType"
					>
						<template v-if="groupedFilteredContent[contentType] && groupedFilteredContent[contentType].length">
						<h5 class="title is-5 is-marginless">{{meta.name}}
							<strong class="content-classifier__result-count">({{getSelectedCountsByContentType(contentType)}}/{{groupedFilteredContent[contentType].length}})</strong>
						</h5>
						<ul
							class="content-classifier__result-list margin bottom"
						>
							<component
								v-for="contentItem in groupedFilteredContent[contentType]"
								:key="contentItem.id"
								:is="meta.component"
								:item="contentItem"
								:is-active="selectedItems.findIndex(item => item.id === contentItem.id && item.type === contentItem.type) > -1"
								@click="toggleSelected(contentItem)"
							/>
						</ul>
						</template>
					</div>
				</div>
				<div v-else>Brak wyników</div>
			</div>
			<div class="content-classifier__panel-editor">
				<wnl-content-classifier-editor
					class="content-classifier__panel-editor__editor"
					v-show="!isLoading"
					:items="selectedItems"
					@taxonomyTermAttached="onTaxonomyTermAttached"
					@taxonomyTermDetached="onTaxonomyTermDetached"
				/>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-classifier
		&__panels
			display: flex
			overflow: hidden

		&__panel-results
			flex: 0 0 50%

			&__header
				display: flex
				justify-content: space-between

		&__panel-editor
			flex: 0 0 50%
			overflow: hidden
			padding-left: $margin-big

			&__editor
				position: sticky
				max-height: calc(100vh - #{2 * $margin-big})
				overflow-y: auto
				top: 0

		.content-classifier__result-count
			font-size: $font-size-minus-2
			font-weight: bold

		&__result-list
			display: flex
			flex-wrap: wrap
</style>

<script>
import axios from 'axios';
import { mapActions } from 'vuex';
import { groupBy } from 'lodash';

import { getApiUrl } from 'js/utils/env';
import { ALERT_TYPES } from 'js/consts/alert';

import WnlHtmlResult from 'js/admin/components/contentClassifier/HtmlResult';
import WnlSlideResult from 'js/admin/components/contentClassifier/SlideResult';
import WnlFlashcardResult from 'js/admin/components/contentClassifier/FlashcardResult';
import WnlAnnotationResult from 'js/admin/components/contentClassifier/AnnotationResult';
import WnlContentClassifierEditor from 'js/components/global/contentClassifier/ContentClassifierEditor';
import WnlContentClassifierFilterByIds from 'js/admin/components/contentClassifier/ContentClassifierFilterByIds';
import WnlContentClassifierFilterByClassification from 'js/admin/components/contentClassifier/ContentClassifierFilterByClassification';
import { parseTaxonomyTermsFromIncludes } from 'js/utils/contentClassifier';
import { CONTENT_TYPES } from 'js/consts/contentClassifier';

const TABS = {
	BY_CLASSIFICATION: 'by-classification',
	BY_ID: 'by-id',
};

export default {
	components: {
		WnlContentClassifierEditor,
		WnlContentClassifierFilterByIds,
		WnlContentClassifierFilterByClassification
	},
	data() {
		const contentTypes = {
			[CONTENT_TYPES.SLIDE]: {
				resourceName: 'slides/.filter',
				name: 'Slajdy',
				component: WnlSlideResult,
			},
			[CONTENT_TYPES.QUIZ_QUESTION]: {
				resourceName: 'quiz_questions/.filter',
				name: 'Pytania zamknięte',
				component: WnlHtmlResult,
			},
			[CONTENT_TYPES.FLASHCARD]: {
				resourceName: 'flashcards/.filter',
				name: 'Pytania otwarte',
				component: WnlFlashcardResult,
			},
			[CONTENT_TYPES.ANNOTATION]: {
				resourceName: 'annotations/.filter',
				name: 'Przypisy',
				component: WnlAnnotationResult,
			},
		};

		return {
			contentTypes,
			filteredContent: null,
			selectedItems: [],
			isLoading: false,
			activeTab: TABS.BY_CLASSIFICATION,
			TABS,
		};
	},
	computed: {
		groupedFilteredContent() {
			return groupBy(this.filteredContent, 'type');
		},
		groupedSelectedItems() {
			return groupBy(this.selectedItems, 'type');
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		fetchContentByIds(contentType, meta, filter) {
			const filters = [];

			if (filter !== '') {
				filters.push({
					by_ids: { ids: filter.split(',') },
				});
			}

			return this.fetchContent(contentType, meta.resourceName, filters);
		},
		fetchContentByTag(contentType, meta, byTagsFilter, byTaxonomyTermsFilter) {
			const filters = [];
			if (byTagsFilter.length) {
				filters.push({
					tags: byTagsFilter.map(tag => tag.id),
				});
			}
			if (byTaxonomyTermsFilter.length) {
				filters.push({
					taxonomy_terms: byTaxonomyTermsFilter.map(tag => tag.id),
				});
			}

			return this.fetchContent(contentType, meta.resourceName, filters);
		},
		async fetchContent(contentType, resourceName, filters) {
			if (filters.length === 0) {
				return [];
			}

			const { data } = await axios.post(getApiUrl(resourceName), {
				filters,
				include: 'taxonomy_terms.tag,taxonomy_terms.taxonomy,taxonomy_terms.ancestors.tag',
				// TODO use wnl-paginated-list instead
				limit: 10000,
			});

			const { data: { included = {}, ...items } } = data;

			return Object.values(items).map(item => {
				item.type = contentType;
				item.taxonomyTerms = parseTaxonomyTermsFromIncludes(item.taxonomy_terms, included);
				return item;
			});
		},
		async onSearchById(filters) {
			const promises = Object.entries(this.contentTypes)
				.map(([contentType, meta]) => this.fetchContentByIds(contentType, meta, filters[contentType]));

			this.onSearch(promises);
		},
		async onSearchByTag({ byTagsFilter, byTaxonomyTermsFilter, activeContentTypesMap }) {
			const promises = Object.entries(this.contentTypes)
				.filter(([contentType]) => activeContentTypesMap[contentType])
				.map(([contentType, meta]) => this.fetchContentByTag(contentType, meta, byTagsFilter, byTaxonomyTermsFilter));

			this.onSearch(promises);
		},
		async onSearch(promises) {
			this.isLoading = true;
			this.selectedItems = [];
			this.filteredContent = [];

			try {
				const values = await Promise.all(promises);

				this.filteredContent = [].concat(...values);
			} catch (error) {
				this.filteredContent = [];

				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Coś poszło nie tak. Spróbuj ponownie.',
					type: ALERT_TYPES.ERROR
				});
			} finally {
				this.isLoading = false;
			}
		},
		onTaxonomyTermAttached(term) {
			this.filteredContent.forEach((item) => {
				if (!item.taxonomyTerms.find(({ id }) => id === term.id)) {
					item.taxonomyTerms.push(term);
				}
			});
		},
		onTaxonomyTermDetached(term) {
			this.filteredContent.forEach((item) => {
				const index = item.taxonomyTerms.findIndex(({ id }) => id === term.id);

				if (index > -1) {
					item.taxonomyTerms.splice(index, 1);
				}
			});
		},
		toggleSelected(contentItem) {
			const index = this.selectedItems.findIndex(item => contentItem.id === item.id && contentItem.type === item.type);
			if (index === -1) {
				this.selectedItems.push(contentItem);
			} else {
				this.selectedItems.splice(index, 1);
			}
		},
		selectAll() {
			this.selectedItems = [...this.filteredContent];
		},
		getSelectedCountsByContentType(contentType) {
			return (this.groupedSelectedItems[contentType] && this.groupedSelectedItems[contentType].length) || 0;
		}
	},
};
</script>
