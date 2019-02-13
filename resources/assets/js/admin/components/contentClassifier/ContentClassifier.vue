<template>
	<div class="content-classifier">
		<h3 class="title is-3">Klasyfikacja treści</h3>
		<form @submit.prevent="onSearch">
			<div v-for="(meta, contentType) in contentTypes" :key="contentType" class="field">
				<label class="label">{{meta.name}}</label>
				<input class="input" placeholder="Wpisz id po przecinku: 36,45,..." v-model="filters[contentType]"/>
			</div>
			<button class="button submit is-primary" type="submit">Szukaj</button>
		</form>
		<div class="content-classifier__panels">
			<div class="content-classifier__panel-results">
				<div class="content-classifier__panel-results__header">
					<h4 class="title is-4 margin bottom">Wyniki wyszukiwania</h4>
					<a @click="selectAll">Zaznacz wszystkie</a>
				</div>
				<div v-if="!isLoading">
					<div v-for="(meta, contentType) in contentTypes" :key="contentType">
						<h5 class="title is-5 is-marginless">{{meta.name}}</h5>
						<ul
							v-if="groupedFilteredContent[contentType] && groupedFilteredContent[contentType].length"
							class="content-classifier__result-list margin bottom"
						>
							<li
								v-for="item in groupedFilteredContent[contentType]"
								:key="item.id"
								class="content-classifier__result-item"
								:class="{'is-active': selectedItemIds.includes(item.id)}"
								@click="toggleSelected(item)"
							>
								<component :is="meta.component" :item="item"/>
								<span class="icon content-classifier__result-item__icon">
									<i class="fa fa-check-circle"></i>
								</span>
							</li>
						</ul>
						<p class="margin bottom" v-else>Brak wyników</p>
					</div>
				</div>
				<wnl-text-loader v-else />
			</div>
			<wnl-content-classifier-editor
				v-show="!isLoading"
				:items="selectedItems"
				@taxonomyTermAttached="onTaxonomyTermAttached"
				@taxonomyTermDetached="onTaxonomyTermDetached"
			/>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-classifier
		&__panels
			display: flex

		&__panel-results
			flex: 50%
			margin-right: $margin-big

			&__header
				display: flex
				justify-content: space-between

		&__result-list
			display: flex
			flex-wrap: wrap

		&__result-item
			border: $border-light-gray
			cursor: pointer
			display: flex
			font-size: $font-size-minus-1
			line-height: $line-height-minus
			margin: $margin-tiny
			max-height: 200px
			min-height: 90px
			overflow: auto
			padding: $margin-base
			position: relative
			transition: border-width .3s ease-in-out, border-color .3s ease-in-out
			width: 160 + 4 * $margin-small

			&__icon
				animation: fadein .3s
				color: $color-correct-shadow
				display: none
				position: absolute
				right: 5px
				top: 5px

				.is-active &
					display: block

			&.is-active
				border: 2px solid $color-correct-shadow
				border-radius: $border-radius-small

	@keyframes fadein
		from
			opacity: 0
		to
			opacity: 1
</style>

<script>
import axios from 'axios';
import {mapActions} from 'vuex';
import {groupBy} from 'lodash';

import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

import WnlHtmlResult from 'js/admin/components/contentClassifier/HtmlResult';
import WnlSlideResult from 'js/admin/components/contentClassifier/SlideResult';
import WnlFlashcardResult from 'js/admin/components/contentClassifier/FlashcardResult';
import WnlAnnotationResult from 'js/admin/components/contentClassifier/AnnotationResult';
import WnlContentClassifierEditor from 'js/components/global/contentClassifier/ContentClassifierEditor';
import {parseTaxonomyTermsFromIncludes} from 'js/utils/contentClassifier';
import {CONTENT_TYPES} from 'js/consts/contentClassifier';

export default {
	components: {
		WnlContentClassifierEditor
	},
	data() {
		const contentTypes = {
			[CONTENT_TYPES.ANNOTATION]: {
				resourceName: 'annotations/.filter',
				name: 'Przypisy',
				component: WnlAnnotationResult,
			},
			[CONTENT_TYPES.QUIZ_QUESTION]: {
				resourceName: 'quiz_questions/.filter',
				name: 'Pytania z bazy pytań',
				component: WnlHtmlResult,
			},
			[CONTENT_TYPES.FLASHCARD]: {
				resourceName: 'flashcards/.filter',
				name: 'Pytania otwarte',
				component: WnlFlashcardResult,
			},
			[CONTENT_TYPES.SLIDE]: {
				resourceName: 'slides/.filter',
				name: 'Slajdy',
				component: WnlSlideResult,
			},
		};

		const filters = Object.keys(contentTypes).reduce(
			(collector, contentType) => {
				collector[contentType] = '';
				return collector;
			},
			{}
		);

		return {
			contentTypes,
			filters,
			filteredContent: [],
			selectedItemIds: [],
			isLoading: false,
		};
	},
	computed: {
		groupedFilteredContent() {
			return groupBy(this.filteredContent, 'type');
		},
		selectedItems() {
			return this.filteredContent.filter(item => this.selectedItemIds.includes(item.id));
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		async fetchContent([contentType, meta]) {
			if (this.filters[contentType] === '') {
				return [];
			}

			const {data} = await axios.post(getApiUrl(meta.resourceName), {
				filters: [
					{
						by_ids: {ids: this.filters[contentType].split(',')},
					},
				],
				include: 'taxonomy_terms.tag,taxonomy_terms.taxonomy,taxonomy_terms.ancestors.tag',
				// TODO use wnl-paginated-list instead
				limit: 10000,
			});

			const {data: {included = {}, ...items}} = data;

			return Object.values(items).map(item => {
				item.type = contentType;
				item.taxonomyTerms = parseTaxonomyTermsFromIncludes(item.taxonomy_terms, included);
				return item;
			});
		},
		async onSearch() {
			this.isLoading = true;
			this.selectedItemIds = [];

			const promises = Object.entries(this.contentTypes).map(this.fetchContent);

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
				if (!item.taxonomyTerms.find(({id}) => id === term.id)) {
					item.taxonomyTerms.push(term);
				}
			});
		},
		onTaxonomyTermDetached(term) {
			this.filteredContent.forEach((item) => {
				const index = item.taxonomyTerms.findIndex(({id}) => id === term.id);

				if (index > -1) {
					item.taxonomyTerms.splice(index, 1);
				}
			});
		},
		toggleSelected(item) {
			const index = this.selectedItemIds.findIndex(itemId => itemId === item.id);
			if (index === -1) {
				this.selectedItemIds.push(item.id);
			} else {
				this.selectedItemIds.splice(index, 1);
			}
		},
		selectAll() {
			this.selectedItemIds = this.filteredContent.map(item => item.id);
		}
	},
};
</script>
