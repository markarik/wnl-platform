<template lang="html">
	<div class="container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Twoje zamówienia
				</div>
			</div>
			<div class="level-right">

			</div>
		</div>
		<wnl-order :order="order" v-for="order in orders"></wnl-order>
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
