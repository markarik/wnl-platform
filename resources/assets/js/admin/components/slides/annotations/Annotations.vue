<template>
	<div>
		<button class="button is-primary" @click="addAnnotation">+ Nowy Przypis</button>
		<div class="tabs">
			<ul>
				<li :class="{'is-active': tab.active}" @click="changeTab(name)" v-for="(tab, name) in tabs" :key="name">
					<a>{{tab.text}}</a>
				</li>
			</ul>
		</div>
		<component
			:is="activeComponent"
			:list="annotations"
			:annotation="activeAnnotation"
			:modifiedAnnotationId="modifiedAnnotationId"
			@annotationSelect="onAnnotationSelect"
			@addSuccess="onAddSuccess"
			@editSuccess="onEditSuccess"
			@deleteSuccess="onDeleteSuccess"
			@hasChanges="onEditorChange"
		>
			<div class="search" slot="search">
				<search @search="search"/>
				<template v-if="searchPhrase">
					<span>Aktualne wyszukiwanie:</span>
					<span class="tag is-success">
						{{searchPhrase}}
						<button class="delete is-small" @click="clearSearch"></button>
					</span>
				</template>
			</div>
		</component>
	</div>
</template>
<script>
	import axios from 'axios';

	import { getApiUrl } from 'js/utils/env'
	import AnnotationsList from "./AnnotationsList";
	import AnnotationsEditor from "./AnnotationsEditor";
	import Search from "./Search";

	export default {
		components: {AnnotationsList, AnnotationsEditor, Search},
		data() {
			return {
				tabs: {
					list: {
						component: AnnotationsList,
						active: true,
						text: 'Lista'
					},
					editor: {
						component: AnnotationsEditor,
						active: false,
						text: 'Edytor'
					}
				},
				activeAnnotation: {},
				annotations: [],
				modifiedAnnotationId: 0,
				searchPhrase: '',
			}
		},
		computed: {
			activeTab() {
				return Object.values(this.tabs).find(tab => tab.active)
			},
			activeComponent() {
				return this.activeTab.component
			},
		},
		methods: {
			async search({phrase, fields}) {
				this.searchPhrase = phrase

				const annotationsResponse = await axios.post(getApiUrl('annotations/.filter'), {
					active: [`search.${this.searchPhrase}`],
					filters: [
						{search: {phrase, fields}}
					],
					include: 'keywords,tags'
				})

				if (!annotationsResponse.data.total) {
					this.annotations = [];
					return;
				}

				this.annotations = this.serializeResponse(annotationsResponse.data)
			},
			async clearSearch() {
				const annotationsResponse = await axios.get(getApiUrl('annotations/all'), {params: {include: 'tags,keywords'}})
				this.searchPhrase = ''
				this.annotations = this.serializeResponse(annotationsResponse);
			},
			changeTab(name) {
				this.activeTab.active = false;
				this.tabs[name].active = true;
			},
			addAnnotation() {
				this.changeTab('editor');
				this.activeAnnotation = {
					tags: [],
					keywords: '',
				};
			},
			onEditorChange(changedAnnotation) {
				this.modifiedAnnotationId = changedAnnotation
			},
			onAnnotationSelect(annotation) {
				if (this.modifiedAnnotationId && annotation.id !== this.modifiedAnnotationId) {
					const result = window.confirm(
						`Masz niezapisane zmiany w przypisie ${this.modifiedAnnotationId}. Czy na pewno chcesz zmienić edytowany przypis?`
					)
					if (result) {
						this.onEditorActivate(annotation)
					}
				} else {
					this.onEditorActivate(annotation)
				}
			},
			onEditorActivate(annotation) {
				this.activeAnnotation = annotation;
				this.activeTab.active = false;
				this.tabs.editor.active = true;
				if (annotation.id !== this.modifiedAnnotationId) {
					this.modifiedAnnotationId = 0
				}
			},
			onAddSuccess(annotation) {
				this.activeAnnotation = {
					...annotation,
					keywords: (annotation.keywords || []).join(',')
				}
				this.annotations.splice(0,0, this.activeAnnotation);
			},
			onEditSuccess(annotation) {
				this.activeAnnotation = {
					...annotation,
						keywords: (annotation.keywords || []).join(',')
				}

				this.annotations = this.annotations.map(item => {
					if (item.id === annotation.id) {
						return {
							...this.activeAnnotation
						}
					}
					return item;
				})
			},
			onDeleteSuccess({id}) {
				this.activeAnnotation = {}

				const annotationIndex = this.annotations.findIndex(annotation => annotation.id === id)
				this.annotations.splice(annotationIndex, 1)
			},
			serializeResponse({data}) {
				const {included, ...annotations} = data;
				const {tags, keywords} = included;

				return Object.values(annotations).map(annotation => {
					return {
						...annotation,
						tags: (annotation.tags || []).map(tagId => ({
							id: tags[tagId].id,
							name: tags[tagId].name,
						})),
						keywords: (annotation.keywords || []).map(keywordId => keywords[keywordId].text).join(',')
					}
				})
			}
		},
		async mounted() {
			const [annotationsResponse, filtersListResponse] = await Promise.all([
				axios.get(getApiUrl('annotations/all'), {params: {include: 'tags,keywords'}}),
				axios.post(getApiUrl('annotations/.filterList'))
			]);

			this.annotations = this.serializeResponse(annotationsResponse);
		},
		beforeRouteLeave(to, from, next) {
			if (this.modifiedAnnotationId) {
				const result = window.confirm(
					`Masz niezapisane zmiany w przypisie ${this.modifiedAnnotationId}. Czy na pewno chcesz wyjść?`
				)
				result && next()
			} else {
				next()
			}
		}
	}
</script>
