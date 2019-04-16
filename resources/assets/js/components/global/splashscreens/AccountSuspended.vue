<template>
	<div>
		<img
			class="splash-screen-image"
			:src="logoImageUrl"
			alt="Logo kursu"
		>
		<div class="has-text-centered">
			<p class="title is-4">Twoje konto zostało zablokowane... 🙁</p>
			<p v-if="instalmentsNotPaid" class="text-dimmed">
				Zaległe raty możesz opłacić w zakładce <router-link :to="{name: 'my-orders'}">KONTO > Twoje zamówienia</router-link>.
			</p>
			<p v-else class="text-dimmed">
				Aby uzyskać więcej informacji napisz do nas na Messengerze
				lub wyślij maila na adres: <a href="mailto:info@wiecejnizlek.pl.">info@wiecejnizlek.pl</a>.
			</p>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.splash-screen-image
		max-width: 400px
		padding: 0 20px
</style>

<script>
import moment from 'moment';
import { PAYMENT_METHODS } from 'js/consts/products';

export default {
	props: {
		orders: {
			type: Array,
			required: true,
		}
	},
	computed: {
		instalmentsNotPaid() {
			return this.orders.filter(order => {
				return order.method === PAYMENT_METHODS.INSTALMENTS && order.paid;
			}).some((order) => {
				return order.instalments.instalments.some(instalment => {
					return instalment.amount > instalment.paid_amount && moment(instalment.due_date).isBefore(new Date(), 'day');
				});
			});
		},
		logoImageUrl() {
			return window.$wnl.course.productLogoWithStudents;
		},
	},
};
</script>
