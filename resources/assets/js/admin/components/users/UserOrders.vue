<template>
	<div>
		<order v-for="(order, index) in user.orders" :orderInstance="order" :key="index"></order>
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
			user: {
				type: Object,
				required: true
			},
		},
	}
</script>
