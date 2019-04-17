<template>
	<div class="orders-list">
		<wnl-paginated-list
			:resource-name="'orders/.filter'"
		>
			<h3 slot="header">Lista zamówień</h3>
			<table
				slot="list"
				slot-scope="slotProps"
				class="table"
			>
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
				<tr
					v-for="order in slotProps.list"
					:key="order.id"
					:class="['row', {'canceled': order.canceled}]"
					@click="goToOrder(order)"
				>
					<td>{{order.id}}</td>
					<td>{{order.created_at}}</td>
					<td>{{order.user_id}}</td>
					<td>{{order.product.name}}</td>
					<td>{{translateShippingStatus(order)}}</td>
					<td>
						<span v-if="order.paid" class="icon has-text-success"><i class="fa fa-check" /></span>
						{{order.paid_amount}} / {{order.total}}PLN
					</td>
					<td>
						<span
							v-if="order.coupon"
							class="icon has-text-success"
							:title="order.coupon.name"
						><i class="fa fa-check" /></span>
					</td>
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

export default {
	name: 'OrdersList',
	components: {
		WnlPaginatedList,
	},
	methods: {
		translateShippingStatus(order) {
			return this.$t(`orders.tags.shipping.${order.shipping_status}`);
		},
		goToOrder(order){
			this.$router.push({ name: 'user-details', params: { userId: order.user_id }, hash: '#orders' });
		}
	},
};
</script>
