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
			<tr>
				<th>ID</th>
				<th>Data</th>
				<th>Id użytkownika</th>
				<th>Produkt</th>
				<th>Status wysyłki</th>
				<th>Wpłata</th>
				<th>Kupon</th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="order in orders" @click="goToOrder(order)" class="row" :key="order.id">
				<td>{{order.id}}</td>
				<td>{{order.created_at}}</td>
				<td>{{order.user_id}}</td>
				<td>{{order.product.name}}</td>
				<td>{{translateShippingStatus(order)}}</td>
				<td>
					<span class="icon has-text-success	" v-if="order.paid"><i class="fa fa-check"></i></span>
					{{order.paid_amount}} / {{order.total}}PLN
				</td>
				<td><span class="icon has-text-success" v-if="order.coupon" :title="order.coupon.name"><i class="fa fa-check"></i></span></td>
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

	.row
		cursor: pointer
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
					params.filters = [{search: {phrase: this.searchPhrase, fields: ['id']}}];
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
			translateShippingStatus(order) {
				return this.$t(`orders.tags.shipping.${order.shipping_status}`)
			},
			goToOrder(order){
				this.$router.push({ name: 'user-details', params: { userId: order.user_id }, hash: '#orders' })
			}
		},
		mounted() {
			this.fetch();
		}
	}
</script>
