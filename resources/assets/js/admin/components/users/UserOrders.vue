<template>
	<div v-if="user.orders.length">
		<wnl-order
			v-for="(order, index) in user.orders"
			:order-instance="order"
			:should-check-payment-status="false"
			:key="index"
		>
			<span slot="order-tags" class="tag">{{$t('orders.tags.shipping.status')}}: {{translateShippingStatus(order)}}</span>
		</wnl-order>
	</div>
	<p v-else>
		Ten użytkownik nie posiada żadnych zamówień.
	</p>
</template>

<style scoped>

</style>

<script>
import WnlOrder from 'js/components/user/Order';

export default {
	name: 'UserOrders',
	components: { WnlOrder },
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
