<template lang="html">
	<div class="wnl-order">
		<h3>Zamówienie #{{ order.id }}</h3>
		<hr>
		<img :src="loaderSrc" v-if="isPending" class="order-loader">
		Data: {{ order.created_at }}<br>
		Produkt: {{ order.product.name }}<br>
		Metoda płatności: {{ paymentMethod }} <a href="">zmień</a> <br>
		Status płatności: {{ paymentStatus }}<br>
		<p v-if="transferDetails">
			Odbiorca: bethink Sp. z o. o.<br>
			Konto: 0000 0000 0000 0000 0000 0000
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	.wnl-order
		margin-top: 20px

		hr
			margin-top: 5px

		h3
			margin-bottom: 0px

		.order-loader
			float: right
</style>

<script>
	import axios from 'axios'
	import {getApiUrl, getImageUrl} from 'js/utils/env'

	export default {
		name: 'Order',
		props: ['order', 'loaderVisible'],
		data() {
			return {
				paymentMethods: {
					'transfer': 'Przelew bankwy',
					'online': 'Szybki przelew online',
				},
			}
		},
		computed: {
			loaderSrc() {
				return getImageUrl('loader.svg')
			},
			isPending () {
				// show loader only if there is an online payment waiting for confirmation
				return !this.order.paid && this.order.method === 'online';
			},
			transferDetails () {
				return !this.order.paid && this.order.method === 'transfer';
			},
			paymentStatus () {
				return this.order.paid ? 'Zapłacono' : 'Oczekuje na zaksięgowanie'
			},
			paymentMethod () {
				return this.paymentMethods[this.order.method];
			}
		},
		methods: {
			checkStatus() {
				axios.get(getApiUrl(`orders/${this.order.id}`))
						.then((response) => {
							if (response.data.paid) {
								this.order.paid = true
							} else {
								setTimeout(this.checkStatus, 5000)
							}
						})
						.catch(console.log.bind(console))
			}
		},
		mounted() {
			if (this.isPending) this.checkStatus()
		}
	}
</script>
