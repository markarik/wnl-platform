<template>
	<div class="splash-screen">
		<div class="splash-screen__container scrollable-main-container">
			<div class="splash-screen__content">
				<wnl-text-loader v-if="isLoading"></wnl-text-loader>
				<template v-else>
					<wnl-splash-screen-account-suspended v-if="currentUserAccountSuspended" :orders="orders"/>
					<wnl-splash-screen-order-canceled v-else-if="allOrdersCanceled"/>
					<wnl-splash-screen-upcoming-edition v-else-if="$upcomingEditionParticipant.isAllowed('access')"/>
					<wnl-splash-screen-order-not-paid v-else-if="latestCourseWaitingForPayment"/>
					<wnl-splash-screen-subscription-expired v-else-if="currentUserSubscriptionStatus === EXPIRED"/>
					<wnl-splash-screen-default v-else/>
				</template>
			</div>
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

	.splash-screen
		display: flex
		flex-direction: column
		height: 100%
		width: 100%

		&__container
			display: flex
			text-align: center
			padding: $margin-small

		&__content
			margin: auto

		&__footer
			border-top: $border-light-gray
			padding: $margin-big

			&__text
				max-width: 320px
</style>

<script>
import axios from 'axios';
import {mapGetters} from 'vuex';

import WnlSplashScreenAccountSuspended from 'js/components/global/splashscreens/AccountSuspended';
import WnlSplashScreenOrderCanceled from 'js/components/global/splashscreens/OrderCanceled';
import WnlSplashScreenUpcomingEdition from 'js/components/global/splashscreens/UpcomingEdition';
import WnlSplashScreenOrderNotPaid from 'js/components/global/splashscreens/OrderNotPaid';
import WnlSplashScreenSubscriptionExpired from 'js/components/global/splashscreens/SubscriptionExpired';
import WnlSplashScreenDefault from 'js/components/global/splashscreens/Default';

import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import {getApiUrl} from 'js/utils/env';
import {PRODUCTS_SLUGS} from 'js/consts/products';
import {SUBSCRIPTION_STATUS} from 'js/consts/user';

export default {
	data() {
		return {
			isLoading: true,
			EXPIRED: SUBSCRIPTION_STATUS.EXPIRED,
			orders: []
		};
	},
	components: {
		WnlSplashScreenAccountSuspended,
		WnlSplashScreenUpcomingEdition,
		WnlSplashScreenOrderNotPaid,
		WnlSplashScreenSubscriptionExpired,
		WnlSplashScreenDefault,
		WnlSplashScreenOrderCanceled
	},
	perimeters: [upcomingEditionParticipant],
	computed: {
		...mapGetters([
			'currentUserAccountSuspended',
			'currentUserSubscriptionStatus',
		]),
		latestCourseOrders() {
			return this.orders.filter(order => order.product.slug === PRODUCTS_SLUGS.SLUG_ONLINE);
		},
		latestCourseWaitingForPayment() {
			return !this.latestCourseOrders.some(order => order.paid && !order.canceled);
		},
		allOrdersCanceled() {
			return this.latestCourseOrders.every(order => order.canceled);
		},
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
