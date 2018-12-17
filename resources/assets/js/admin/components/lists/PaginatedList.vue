<template>
	<div class="list">
		<slot name="header">
			<h3 class="title">Lista</h3>
		</slot>

		<wnl-search-input @search="onSearch" class="search" :availableFields="searchAvailableFields" />
		<wnl-pagination
			v-if="lastPage > 1"
			:currentPage="page"
			:lastPage="lastPage"
			@changePage="onPageChange"
			class="pagination"
		/>

		<template v-if="!isLoading">
			<slot name="list" v-if="!isEmpty(list)" :list="list" />
			<div class="title is-6" v-else>Nic tu nie ma...</div>
		</template>
		<wnl-text-loader v-else></wnl-text-loader>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.pagination /deep/ .pagination-list
		justify-content: center
		margin-bottom: $margin-medium

	.search
		margin-bottom: $margin-base
</style>

<script>
	import {mapActions} from 'vuex';
	import {isEmpty} from 'lodash';

	import WnlPagination from 'js/components/global/Pagination';
	import WnlSearchInput from 'js/components/global/SearchInput';

export default {
	components: {
		WnlPagination,
		WnlSearchInput,
	},
	data() {
		return {
			list: [],
			searchPhrase: '',
			lastPage: 1,
			page: 1,
			isLoading: true,
			searchFields: [],
		};
	},
	props: {
		searchAvailableFields: {
			type: Array,
			default: () => [],
		},
		resourceUrl: {
			type: String,
			required: true,
		},
		customRequestParams: {
			type: Object,
			default: () => ({}),
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		isEmpty,
		getRequestParams() {
			const params = {
				limit: 50,
				page: this.page,
				active: [],
				filters: []
			};

			if (this.searchPhrase) {
				params.active = [`search.${this.searchPhrase}`];
				params.filters = [{search: {phrase: this.searchPhrase, fields: this.searchFields}}];
			}

			return {
				...params,
				...this.customRequestParams,
			};
		},
		onPageChange(page) {
			this.page = page;
			this.fetch();
		},
		async onSearch({phrase, fields}) {
			this.page = 1;
			this.searchPhrase = phrase;
			this.searchFields = fields;

			await this.fetch();
		},
		async fetch() {
			try {
				this.isLoading = true;
				const response = await axios.post(this.resourceUrl, this.getRequestParams());
				const {data: {data, ...paginationMeta}} = response;
				this.list = data;
				this.lastPage = paginationMeta.last_page;
			} catch (error) {
				this.addAutoDismissableAlert({
					text: 'Ops, nie udało się pobrać listy. Odśwież stronę i spróbuj jeszcze raz',
					type: 'error'
				});
				$wnl.logger.capture(error);
			} finally {
				this.isLoading = false;
			}
		},
	},
	mounted() {
		this.fetch();
	},
	watch: {
		customRequestParams() {
			this.page = 1;
			this.fetch();
		}
	}
};
</script>
