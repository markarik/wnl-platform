<template>
	<div>
		<order v-for="(order, index) in orders" :orderInstance="order" :key="index"></order>
	</div>
</template>

<style scoped>

</style>

<script>
	import Order from 'js/components/user/Order'

	export default {
		name: "UserOrders",
		components: { Order },
		props: {
			orders: {
				type: Array,
				required: true
			},
		},
	}
</script>
