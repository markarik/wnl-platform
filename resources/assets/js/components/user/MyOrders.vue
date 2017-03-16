<template lang="html">
	<div class="container">
		<h1>Twoje zamówienia</h1>

		<wnl-order v-for="order in orders" :order="order">
		</wnl-order>
	</div>

</template>

<script>
	import axios from 'axios'
	import {getApiUrl, getImageUrl} from 'js/utils/env'
	import Order from './Order'

	export default {
		name: 'MyOrders',
		data () {
			return {
				orders: {}
			}
		},
		methods: {
			getOrders() {
				axios.get(getApiUrl(`orders/all`))
						.then((response) => {
							this.orders = response.data
						})
						.catch(console.log.bind(console))
			}
		},
		mounted() {
			this.getOrders()
		},
		components: {
			'wnl-order': Order,
		}
	}
</script>
