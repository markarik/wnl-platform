<template>
	<div>
		<users-list
				:list="users"
				:annotation="activeAnnotation"
				:modifiedAnnotationId="modifiedAnnotationId"
				@annotationSelect="onAnnotationSelect"
				@addSuccess="onAddSuccess"
				@editSuccess="onEditSuccess"
				@deleteSuccess="onDeleteSuccess"
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
			<pagination v-if="paginationMeta.last_page > 1"
			            :currentPage="page"
			            :lastPage="paginationMeta.last_page"
			            @changePage="onPageChange"
			            slot="pagination"
			            class="annotations__pagination"
			/>
		</users-list>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.annotations__pagination .pagination-list
		justify-content: center
		margin-top: $margin-medium
</style>

<script>
	import axios from 'axios';
	import {mapActions} from 'vuex'

	import { getApiUrl } from 'js/utils/env'
	import UsersList from "./UsersList";
	import Pagination from "js/components/global/Pagination";
	import Search from "./Search";

	export default {
		components: {UsersList, Search, Pagination},
		data() {
			return {
				activeAnnotation: {},
				annotations: [],
				roles: [],
				modifiedAnnotationId: 0,
				searchPhrase: '',
				searchFields: [],
				perPage: 24,
				page: 1,
				includes: 'roles',
				paginationMeta: {}
			}
		},
		computed: {

		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			async search({phrase, fields}) {
				this.page = 1
				this.searchPhrase = phrase
				this.searchFields = fields

				await this.fetchUsers('users/.filter', 'post')
			},
			async clearSearch() {
				this.searchPhrase = ''
				this.searchFields = []
				this.page = 1
				await this.fetchUsers()
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
			async onPageChange(page) {
				this.page = page
				await this.fetchUsers()
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
				this.users.splice(0,0, this.activeAnnotation);
			},
			onEditSuccess(annotation) {
				this.activeAnnotation = {
					...annotation,
					keywords: (annotation.keywords || []).join(',')
				}

				this.users = this.users.map(item => {
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

				const annotationIndex = this.users.findIndex(annotation => annotation.id === id)
				this.users.splice(annotationIndex, 1)
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
			async fetchUsers(url = 'users/all', method = 'get') {
				try {
					const params = method === 'get' ? {
						params: this.getRequestParams()
					} : this.getRequestParams()
					const {data: response} = await axios[method](getApiUrl(url), params)

					const {data, ...paginationMeta} = response
					this.paginationMeta = paginationMeta
					if (paginationMeta.total === 0) {
						this.users = []
					} else {
						const {included, ...users} = data
						this.users = Object.values(users)
						this.roles = included.roles
					}
				} catch (e) {
					this.addAutoDismissableAlert({
						text: "Ops, nie udało się pobrać przypisów. Odśwież stronę i spróbuj jeszcze raz",
						type: 'error'
					})
					console.error(e)
				}
			},
		},
		async mounted() {
			await this.fetchUsers()
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
