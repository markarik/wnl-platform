<template>
	<div class="list">
		<slot name="header">
			<h3 class="title">Lista</h3>
		</slot>

		<wnl-search-input
			class="search"
			v-if="isSearchEnabled"
			@search="onSearch"
			:available-fields="searchAvailableFields"
		/>
		<wnl-pagination
			v-if="lastPage > 1"
			:current-page="page"
			:last-page="lastPage"
			@changePage="onPageChange"
			class="pagination"
		/>

		<template v-if="!isLoading">
			<slot name="list" v-if="!isEmpty(list)" :list="list"/>
			<div class="title is-6" v-else>Nic tu nie ma...</div>
		</template>
		<wnl-text-loader v-else></wnl-text-loader>

		<wnl-pagination
			v-if="lastPage > 1"
			:current-page="page"
			:last-page="lastPage"
			@changePage="onPageChange"
			class="pagination"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.pagination /deep/ .pagination-list
		justify-content: center
		margin: $margin-base 0

	.search
		margin-bottom: $margin-base
</style>

<script>
import axios from 'axios';
import {mapActions} from 'vuex';
import {isEmpty} from 'lodash';

import WnlPagination from 'js/components/global/Pagination';
import WnlSearchInput from 'js/components/global/SearchInput';
import {getApiUrl} from 'js/utils/env';

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
		resourceName: {
			type: String,
			required: true,
		},
		customRequestParams: {
			type: Object,
			default: () => ({}),
		},
		dirty: {
			type: Boolean,
			default: false
		},
		isSearchEnabled: {
			type: Boolean,
			default: true,
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
			this.searchPhrase = phrase;
			this.searchFields = fields;

			await this.fetch();
		},
		async fetch() {
			try {
				this.isLoading = true;
				if (this.requestCancelTokenSource) {
					// cancel previous request
					this.requestCancelTokenSource.cancel();
				}
				this.requestCancelTokenSource = axios.CancelToken.source();

				const response = await axios.post(
					getApiUrl(this.resourceName),
					this.getRequestParams(),
					{ cancelToken: this.requestCancelTokenSource.token }
				);
				const {data: {data, ...paginationMeta}} = response;
				this.list = data;
				this.lastPage = paginationMeta.last_page;
				this.isLoading = false;
			} catch (error) {
				if (!axios.isCancel(error)) {
					this.addAutoDismissableAlert({
 						text: 'Ups, nie udało się pobrać listy. Odśwież stronę, żeby spróbować ponownie.',
						type: 'error'
					});
					$wnl.logger.capture(error);
					this.isLoading = false;
				}
			}
		},
	},
	mounted() {
		this.fetch();
	},
	watch: {
		customRequestParams() {
			this.fetch();
		},
		async dirty() {
			if (this.dirty) {
				await this.fetch();
			}

			this.$emit('updated');
		}
	}
};
</script>
