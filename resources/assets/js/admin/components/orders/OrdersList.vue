<template>
	<div class="orders-list">
		<h3 class="title">Lista zamówień</h3>

		<wnl-search-input @search="onSearch" />
		<wnl-pagination
			v-if="lastPage > 1"
			:currentPage="page"
			:lastPage="lastPage"
			@changePage="onPageChange"
			class="pagination"
		/>

		<table class="table">
			<thead>
			<th>ID</th>
			<th>Data</th>
			<th>Produkt</th>
			<th>Status</th>
			<th>Wpłata</th>
			</thead>
			<tbody>
			<tr v-for="order in orders">
				<td>{{order.id}}</td>
				<td>{{order.created_at}}</td>
				<td>{{order.product.name}}</td>
				<td>{{order.status}}</td>
				<td>{{order.paid_amount}} / {{order.total}}PLN</td>
			</tr>
			</tbody>
		</table>

	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.pagination /deep/ .pagination-list
		justify-content: center
		margin-bottom: $margin-medium
</style>

<script>
	import WnlPagination from 'js/components/global/Pagination';
	import WnlSearchInput from 'js/components/global/SearchInput';
	import {getApiUrl} from 'js/utils/env';

	export default {
		name: 'OrdersList',
		components: {
			WnlPagination,
			WnlSearchInput,
		},
		data() {
			return {
				orders: [],
				searchPhrase: '',
				lastPage: 1,
				page: 1,
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
				const response = await axios.post(getApiUrl('orders/.filter'), this.getRequestParams())
				const {data: {data, ...paginationMeta}} = response;
				this.orders = data;
				this.lastPage = paginationMeta.last_page;
			},
		},
		mounted() {
			this.fetch();
		}
	}
</script>
