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

		<h4 class="title is-4 margin bottom">Wyniki wyszukiwania</h4>
		<div v-for="(meta, contentType) in contentTypes" :key="contentType" v-if="filteredContent[contentType].length">
			<h5 class="title is-5 is-marginless">{{meta.name}}</h5>
			<ul class="content-classifier-result-list margin bottom">
				<li v-for="item in filteredContent[contentType]" class="content-classifier-result-item">
					<component :is="meta.component" :item="item"/>
				</li>
			</ul>
		</div>

	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-classifier
		&-result-list
			display: flex
			flex-wrap: wrap

		&-result-item
			border: $border-light-gray
			display: flex
			font-size: $font-size-minus-1
			line-height: $line-height-minus
			margin: $margin-small
			max-height: 200px
			min-height: 90px
			overflow: auto
			padding: $margin-small
			width: 160 + 4 * $margin-small
</style>

<script>
import {getApiUrl} from 'js/utils/env';
import WnlHtmlResult from 'js/admin/components/contentClassifier/HtmlResult';
import WnlSlideResult from 'js/admin/components/contentClassifier/SlideResult';
import WnlFlashcardResult from 'js/admin/components/contentClassifier/FlashcardResult';
import WnlAnnotationResult from 'js/admin/components/contentClassifier/AnnotationResult';

export default {
	data() {
		const contentTypes = {
			annotations: {
				resourceName: 'annotations/.filter',
				name: 'Przypisy',
				component: WnlAnnotationResult,
			},
			quizQuestions: {
				resourceName: 'quiz_questions/.filter',
				name: 'Pytania z bazy pytań',
				component: WnlHtmlResult,
			},
			flashcards: {
				resourceName: 'flashcards/.filter',
				name: 'Pytania otwarte',
				component: WnlFlashcardResult,
			},
			slides: {
				resourceName: 'slides/.filter',
				name: 'Slajdy',
				component: WnlSlideResult,
			},
		};

		const filtersSetup = Object.keys(contentTypes).reduce(
			(collector, contentType) => {
				collector.filters[contentType] = '';
				collector.filteredContent[contentType] = [];
				return collector;
			},
			{
				filters: {},
				filteredContent: {}
			}
		);

		return {
			contentTypes,
			...filtersSetup
		};
	},
	methods: {
		async onSearch() {
			Object.entries(this.contentTypes).forEach(
				async ([contentType, meta]) => {
					if (this.filters[contentType] === '') {
						return;
					}

					const {data: {data}} = await axios.post(getApiUrl(meta.resourceName), {
						filters: [
							{
								by_ids: {ids: this.filters[contentType].split(',')},
							},
						],
					});

					this.filteredContent[contentType] = data;
				}
			);
		}
	}
};
</script>
