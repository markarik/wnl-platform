<template>
	<div v-if="user.orders.length">
		<order v-for="(order, index) in user.orders" :orderInstance="order" :key="index">
			<span slot="order-tags" class="tag">{{$t('orders.tags.shipping.status')}}: {{translateShippingStatus(order)}}</span>
		</order>
	</div>
	<p v-else>
		Ten użytkownik nie posiada żadnych zamówień.
	</p>
</template>

<style scoped>

</style>

<script>
import Order from 'js/components/user/Order';

export default {
	name: 'UserOrders',
	components: { Order },
	props: {
		user: {
			type: Object,
			required: true
		},
	},
	methods: {
		translateShippingStatus(order) {
			return this.$t(`orders.tags.shipping.${order.shipping_status}`);
		}
	}
};
</script>
