<template>
	<div class="flashcards-list">
		<h3 class="title">Lista pytań</h3>
		<router-link :to="{ name: 'flashcards-edit', params: { flashcardId: 'new' } }" class="button is-success margin bottom">+ Nowe pytanie</router-link>

		<wnl-search-input @search="onSearch" class="flashcards-search"/>
		<wnl-pagination
			v-if="lastPage > 1"
			:currentPage="page"
			:lastPage="lastPage"
			@changePage="onPageChange"
			class="pagination"
		/>

		<div v-if="!isLoading">
			<wnl-flashcard-list-item v-for="flashcard in flashcards"
															 :key="flashcard.id"
															 :content="flashcard.content"
															 :id="flashcard.id"
			/>
		</div>
		<wnl-text-loader v-else></wnl-text-loader>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.flashcards-search
		/deep/ .active-search
			margin-top: $margin-base

	.search
		margin-bottom: $margin-base

	.pagination /deep/ .pagination-list
		justify-content: center
		margin-bottom: $margin-medium
</style>

<script>
import WnlPagination from 'js/components/global/Pagination';
import FlashcardsListItem from 'js/admin/components/flashcards/list/FlashcardsListItem';
import WnlSearchInput from 'js/components/global/SearchInput';
import {getApiUrl} from 'js/utils/env';

export default {
	name: 'FlashcardsList',
	components: {
		'wnl-flashcard-list-item': FlashcardsListItem,
		WnlPagination,
		WnlSearchInput,
	},
	data() {
		return {
			flashcards: [],
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

			await this.fetch();
		},
		async fetch() {
			this.isLoading = true;
			const response = await axios.post(getApiUrl('flashcards/.filter'), this.getRequestParams());
			const {data: {data, ...paginationMeta}} = response;
			this.flashcards = data;
			this.lastPage = paginationMeta.last_page;

			this.isLoading = false;
		},
	},
	mounted() {
		this.fetch();
	}
};
</script>
