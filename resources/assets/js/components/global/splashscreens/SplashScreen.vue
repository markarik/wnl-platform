<template>
	<div class="splash-screen__container">
		<div class="splash-screen__content scrollable-main-container">
			<wnl-text-loader v-if="isLoading"></wnl-text-loader>
			<template v-else>
				<wnl-account-suspended v-if="currentUserAccountSuspended" :instalments-not-paid="instalmentsNotPaid"/>
				<wnl-order-canceled v-else-if="orderCanceled"/>
				<wnl-upcoming-edition v-else-if="$upcomingEditionParticipant.isAllowed('access')"/>
				<wnl-order-not-paid v-else-if="latestCourseWaitingForPayment" />
				<wnl-default-splash-screen v-else />
			</template>
		</div>
		<footer class="splash-screen__footer text-dimmed">
			<p class="splash-screen__footer__text">
				W razie problemów napisz do nas na Messengerze lub wyślij maila na adres: <a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl</a>
			</p>
		</footer>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.splash-screen__container
		display: flex
		flex-direction: column
		height: 100%
		width: 100%

	.splash-screen__content
		align-items: center
		display: flex
		flex-direction: column
		justify-content: center
		height: 100%
		text-align: center
		padding: $margin-small

	.splash-screen__footer
		border-top: $border-light-gray
		padding: $margin-big

		&__text
			max-width: 320px
</style>

<script>
import axios from 'axios';
import {mapGetters} from 'vuex';
import moment from 'moment';

import WnlAccountSuspended from 'js/components/global/splashscreens/AccountSuspended';
import WnlOrderCanceled from 'js/components/global/splashscreens/OrderCanceled';
import WnlUpcomingEdition from 'js/components/global/splashscreens/UpcomingEdition';
import WnlOrderNotPaid from 'js/components/global/splashscreens/OrderNotPaid';
import WnlDefaultSplashScreen from 'js/components/global/splashscreens/Default';

import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import {getApiUrl} from 'js/utils/env';
import {PRODUCTS_SLUGS} from 'js/consts/products';

export default {
	data() {
		return {
			isLoading: true,
			orders: []
		};
	},
	components: { WnlAccountSuspended, WnlUpcomingEdition, WnlOrderNotPaid, WnlDefaultSplashScreen, WnlOrderCanceled },
	perimeters: [upcomingEditionParticipant],
	computed: {
		...mapGetters(['currentUserAccountSuspended']),
		latestCourseOrders() {
			return this.orders.filter(order => order.product.slug === PRODUCTS_SLUGS.SLUG_ONLINE);
		},
		latestCourseWaitingForPayment() {
			return !this.latestCourseOrders.some(order => order.paid && !order.canceled);
		},
		orderCanceled() {
			return !this.latestCourseOrders.some(order => order.paid)
					&& this.latestCourseOrders.some(order => order.canceled);
		},
		instalmentsNotPaid() {
			return this.orders.filter(order => {
				return order.method === 'instalments' && order.paid;
			}).some((order) => {
				return order.instalments.instalments.some(instalment => {
					return instalment.amount > instalment.paid_amount && moment(instalment.due_date).isBefore(new Date(), 'day');
				});
			});
		}
	},
	methods: {
		async fetchOrders() {
			const {data: orders} = await axios.get(getApiUrl('orders/all'));
			this.orders = orders;
		},
	},
	async mounted() {
		try {
			await this.fetchOrders();
		} catch (e) {
			$wnl.logger.capture(e);
		} finally {
			this.isLoading = false;
		}
	}
};
</script>
