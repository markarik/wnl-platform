<template>
	<div>
		<div class="header">
			<div class="tabs">
				<ul>
					<li :class="{'is-active': tab.active, [tab.class]: tab.class}" @click="changeTab(name, tab)" v-for="(tab, name) in tabs" :key="name">
						<a>{{tab.text}}</a>
					</li>
				</ul>
			</div>
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
			<wnl-search-input @search="search" :availableFields="searchAvailableFields" slot="search" />
			<pagination v-if="paginationMeta.last_page > 1"
				:currentPage="page"
				:lastPage="paginationMeta.last_page"
				@changePage="onPageChange"
				slot="pagination"
				class="annotations__pagination"
			/>
			<pagination v-if="paginationMeta.last_page > 1"
				:currentPage="page"
				:lastPage="paginationMeta.last_page"
				@changePage="onPageChange"
				slot="pagination-bottom"
				class="annotations__pagination"
			/>
		</component>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.annotations__pagination
		margin-top: $margin-base

		.pagination-list
			justify-content: center

	.header
		background: white
		position: sticky
		top: -30px
		z-index: 100
		padding-bottom: $margin-small

	.tabs
		margin-bottom: 0
		.highlighted
			width: 100%
			text-align: right
			a
				background-color: $color-ocean-blue
				color: white
				display: inline-block
			&.is-active a
				color: #fff
	.search
		position: sticky
		top: 13px
		background: white
		padding: $margin-small 0
		z-index: 100
		+small-shadow-bottom()
</style>

<script>
	import axios from 'axios';
	import {mapActions} from 'vuex'

	import { getApiUrl } from 'js/utils/env'
	import AnnotationsList from "./AnnotationsList";
	import AnnotationsEditor from "./AnnotationsEditor";
	import Pagination from "js/components/global/Pagination";
	import WnlSearchInput from 'js/components/global/SearchInput';

	export default {
		components: {AnnotationsList, AnnotationsEditor, WnlSearchInput, Pagination},
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
					},
					new: {
						component: AnnotationsEditor,
						active: false,
						text: '+ Nowy Przypis',
						clickCallback: () => {
							this.activeAnnotation = {
								tags: [],
								keywords: '',
							};
						},
						class: 'highlighted'
					}
				},
				activeAnnotation: {},
				annotations: [],
				modifiedAnnotationId: 0,
				searchPhrase: '',
				searchFields: [],
				perPage: 24,
				page: 1,
				includes: 'keywords,tags',
				paginationMeta: {},
				searchAvailableFields: [
					{value: 'id', title: 'ID'},
					{value: 'title', title: 'Tytuł'},
					{value: 'description', title: 'Treść'},
					{value: 'tags.name', title: 'Nazwa Taga'},
				]
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
			...mapActions(['addAutoDismissableAlert']),
			async search({phrase, fields}) {
				this.page = 1
				this.searchPhrase = phrase
				this.searchFields = fields

				await this.fetchAnnotations('annotations/.filter', 'post')
			},
			async clearSearch() {
				this.searchPhrase = ''
				this.searchFields = []
				this.page = 1
				await this.fetchAnnotations()
			},
			changeTab(name, tab) {
				this.activeTab.active = false;
				this.tabs[name].active = true;
				if (typeof tab.clickCallback === 'function') tab.clickCallback();
			},
			onEditorChange(changedAnnotation) {
				this.modifiedAnnotationId = changedAnnotation
			},
			async onPageChange(page) {
				this.page = page
				await this.fetchAnnotations()
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
			},
			getRequestParams() {
				const params = {
					include: this.includes,
					limit: this.perPage,
					page: this.page,
					active: [],
					filters: []
				}

				if (this.searchPhrase) {
					params.active = [`search.${this.searchPhrase}`]
					params.filters = [{search: {phrase: this.searchPhrase, fields: this.searchFields}}]
				}
				return params
			},
			async fetchAnnotations(url = 'annotations/all', method = 'get') {
				try {
					const params = method === 'get' ? {
						params: this.getRequestParams()
					} : this.getRequestParams()
					const annotationsResponse = await axios[method](getApiUrl(url), params)

					const {data: response} = annotationsResponse
					const {data, ...paginationMeta} = response
					this.paginationMeta = paginationMeta
					if (paginationMeta.total === 0) {
						this.annotations = []
					} else {
						this.annotations = this.serializeResponse(response);
					}
				} catch (e) {
					this.addAutoDismissableAlert({
						text: "Ops, nie udało się pobrać przypisów. Odśwież stronę i spróbuj jeszcze raz",
						type: 'error'
					})
				}
			},
		},
		async mounted() {
			await this.fetchAnnotations()
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
