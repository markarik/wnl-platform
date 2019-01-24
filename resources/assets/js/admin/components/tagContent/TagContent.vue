<template>
	<div class="orders-list">
		<h3 class="title is-3">Tagowanie treści</h3>
		<form @submit.prevent="onSearch">
			<div v-for="(meta, taggableType) in taggableTypes" :key="taggableType" class="field">
				<label class="label">{{meta.name}}</label>
				<input class="input" placeholder="36,45,..." v-model="filters[taggableType]"/>
			</div>
			<button class="button submit is-primary" type="submit">Szukaj</button>
		</form>

		<h4>Wyszukane</h4>
		<div v-for="(meta, taggableType) in taggableTypes" :key="taggableType">
			<h5 v-if="filtered[taggableType].length" class="title is-5">{{meta.name}}</h5>
			<ul>
				<!-- TODO handle quiz questions HTML markup -->
				<li v-for="item in filtered[taggableType]">{{item.name || item.title || item.text}} (id: {{item.id}})</li>
			</ul>
		</div>

	</div>
</template>

<style lang="sass">
</style>

<script>
	import {getApiUrl} from 'js/utils/env';

	export default {
		data() {
			const taggableTypes = {
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
				},
				slides: {
					resourceName: 'slides/.filter',
					name: 'Slajdy',
				},
			};

			const filters = Object.keys(taggableTypes).reduce(
				(collector, taggableType) => {
					collector[taggableType] = '';
					return collector;
				},
				{}
			);

			const filtered = Object.keys(taggableTypes).reduce(
				(collector, taggableType) => {
					collector[taggableType] = [];
					return collector;
				},
				{}
			);

			return {
				taggableTypes,
				filters,
				filtered,
			}
		},
		methods: {
			async onSearch() {
				Object.entries(this.taggableTypes).forEach(
					async ([taggableType, taggableMeta]) => {
						if (this.filters[taggableType] === '') {
							return;
						}

						const {data: {data}} = await axios.post(getApiUrl(taggableMeta.resourceName), {
							filters: [
								{
									by_ids: {ids: this.filters[taggableType].split(',')},
								},
							],
						});

						this.filtered[taggableType] = data;
					}
				);
			}
		}
	};
</script>
