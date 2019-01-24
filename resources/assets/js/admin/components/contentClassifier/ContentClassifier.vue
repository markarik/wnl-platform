<template>
	<div class="orders-list">
		<h3 class="title is-3">Tagowanie treści</h3>
		<form @submit.prevent="onSearch">
			<div v-for="(meta, contentType) in contentTypes" :key="contentType" class="field">
				<label class="label">{{meta.name}}</label>
				<input class="input" placeholder="36,45,..." v-model="filters[contentType]"/>
			</div>
			<button class="button submit is-primary" type="submit">Szukaj</button>
		</form>

		<h4>Wyszukane</h4>
		<div v-for="(meta, contentType) in contentTypes" :key="contentType" class="content">
			<h5 v-if="filtered[contentType].length" class="title is-5">{{meta.name}}</h5>
			<ul>
				<!-- TODO handle quiz questions HTML markup -->
				<li v-for="item in filtered[contentType]">
					<span v-if="!meta.component">{{item.name || item.title || item.text}} (id: {{item.id}})</span>
					<component v-else :is="meta.component" :item="item"/>
				</li>
			</ul>
		</div>

	</div>
</template>

<style lang="sass">
</style>

<script>
import {getApiUrl} from 'js/utils/env';
import WnlHtmlResult from 'js/admin/components/contentClassifier/HtmlResult';

export default {
	data() {
		const contentTypes = {
			lessons: {
				resourceName: 'lessons/.filter',
				name: 'Lekcje',
			},
			pages: {
				resourceName: 'pages/.filter',
				name: 'Strony',
			},
			annotations: {
				resourceName: 'annotations/.filter',
				name: 'Przypisy',
			},
			screens: {
				resourceName: 'screens/.filter',
				name: 'Ekrany',
			},
			quizQuestions: {
				resourceName: 'quiz_questions/.filter',
				name: 'Pytania z bazy pytań',
				component: WnlHtmlResult
			},
			slides: {
				resourceName: 'slides/.filter',
				name: 'Slajdy',
			},
		};

		const filters = Object.keys(contentTypes).reduce(
			(collector, contentType) => {
				collector[contentType] = '';
				return collector;
			},
			{}
		);

		const filtered = Object.keys(contentTypes).reduce(
			(collector, contentType) => {
				collector[contentType] = [];
				return collector;
			},
			{}
		);

		return {
			contentTypes,
			filters,
			filtered,
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

					this.filtered[contentType] = data;
				}
			);
		}
	}
};
</script>
