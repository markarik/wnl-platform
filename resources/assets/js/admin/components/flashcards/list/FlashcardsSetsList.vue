<template>
	<div class="flashcards-list" >
		<h3 class="title">Lista zestawów pytań</h3>
		<router-link :to="{ name: 'flashcards-sets-edit', params: { flashcardsSetId: 'new' } }" class="button is-success margin bottom">+ Nowy zestaw</router-link>

		<wnl-search-input @search="onSearch" class="search" />
		<wnl-pagination
			v-if="lastPage > 1"
			:currentPage="page"
			:lastPage="lastPage"
			@changePage="onPageChange"
			class="pagination"
		/>

		<div v-if="!isLoading">
			<wnl-flashcards-sets-list-item v-for="flashcardsSet in flashcardsSets"
																		 :key="flashcardsSet.id"
																		 :name="flashcardsSet.name"
																		 :id="flashcardsSet.id"
			/>
		</div>
		<wnl-text-loader v-else></wnl-text-loader>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.search
		margin: $margin-big 0

		/deep/ .active-search
			margin-top: $margin-base

	.pagination /deep/ .pagination-list
		justify-content: center
		margin-bottom: $margin-medium
</style>

<script>
	import WnlFlashcardsSetsListItem from 'js/admin/components/flashcards/list/FlashcardsSetsListItem';
	import WnlPagination from 'js/components/global/Pagination';
	import WnlSearchInput from 'js/components/global/SearchInput';
	import {getApiUrl} from 'js/utils/env';

	export default {
		name: 'FlashcardsSetList',
		components: {
			WnlFlashcardsSetsListItem,
			WnlPagination,
			WnlSearchInput
		},
		data() {
			return {
				flashcardsSets: [],
				searchPhrase: '',
				lastPage: 1,
				page: 1,
				isLoading: true
			};
		},
		methods: {
			getRequestParams() {
				const params = {
					limit: 50,
					page: this.page,
					active: [],
					filters: []
				};

				if (this.searchPhrase) {
					params.active = [`search.${this.searchPhrase}`];
					params.filters = [{search: {phrase: this.searchPhrase, fields: []}}];
				}

				return params;
			},
			onPageChange(page) {
				this.page = page;
				this.fetch();
			},
			async onSearch({phrase}) {
				this.page = 1;
				this.searchPhrase = phrase;

				await this.fetch()
			},
			async fetch() {
				this.isLoading = true;
				const response = await axios.post(getApiUrl('flashcards_sets/.filter'), this.getRequestParams())
				const {data: {data, ...paginationMeta}} = response;
				this.flashcardsSets = data;
				this.lastPage = paginationMeta.last_page;
				this.isLoading = false;
			},
		},
		mounted() {
			this.fetch();
		}
	}
</script>
