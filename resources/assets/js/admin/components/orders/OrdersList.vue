<template>
	<div class="orders-list">
		<wnl-paginated-list
			:resourceUrl="getApiUrl('orders/.filter')"
		>
			<h3 slot="header">Lista zamówień</h3>
			<table class="table" slot-scope="slotProps" slot="list">
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
				<tr v-for="order in slotProps.list" @click="goToOrder(order)" :class="['row', {'canceled': order.canceled}]" :key="order.id">
					<td>{{order.id}}</td>
					<td>{{order.created_at}}</td>
					<td>{{order.user_id}}</td>
					<td>{{order.product.name}}</td>
					<td>{{translateShippingStatus(order)}}</td>
					<td>
						<span class="icon has-text-success" v-if="order.paid"><i class="fa fa-check"></i></span>
						{{order.paid_amount}} / {{order.total}}PLN
					</td>
					<td><span class="icon has-text-success" v-if="order.coupon" :title="order.coupon.name"><i class="fa fa-check"></i></span></td>
				</tr>
				</tbody>
			</table>
		</wnl-paginated-list>

	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.row
		cursor: pointer

		&.canceled
			opacity: .2
</style>

<script>
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';
import {getApiUrl} from 'js/utils/env';

export default {
	name: 'OrdersList',
	components: {
		WnlPaginatedList,
	},
	methods: {
		getApiUrl,
		translateShippingStatus(order) {
			return this.$t(`orders.tags.shipping.${order.shipping_status}`);
		},
		goToOrder(order){
			this.$router.push({ name: 'user-details', params: { userId: order.user_id }, hash: '#orders' });
		}
	},
};
</script>
