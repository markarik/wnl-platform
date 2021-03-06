<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
			:is-narrow="true"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted" />
		</wnl-sidenav-slot>

		<div class="splash-screen">
			<div class="splash-screen__container scrollable-main-container">
				<div class="splash-screen__content">
					<wnl-text-loader v-if="isLoading" />
					<template v-else>
						<wnl-splash-screen-generic-error v-if="currentUserLoadingError" />
						<wnl-splash-screen-account-suspended v-else-if="currentUserAccountSuspended" :orders="orders" />
						<wnl-splash-screen-order-canceled v-else-if="allOrdersCanceled" />
						<wnl-splash-screen-upcoming-edition v-else-if="$upcomingEditionParticipant.isAllowed('access')" />
						<wnl-splash-screen-order-not-paid v-else-if="latestCourseWaitingForPayment" />
						<wnl-splash-screen-subscription-expired v-else-if="currentUserSubscriptionStatus === EXPIRED" />
						<wnl-splash-screen-default v-else />
					</template>
				</div>
			</div>
			<footer class="splash-screen__footer text-dimmed">
				<p class="splash-screen__footer__text">
					W razie problemów napisz do nas na Messengerze lub wyślij maila na adres: <a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl</a>
				</p>
			</footer>
		</div>
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
import { mapGetters } from 'vuex';

import WnlMainNav from 'js/components/MainNav';
import WnlSidenavSlot from 'js/components/global/SidenavSlot';
import WnlSplashScreenAccountSuspended from 'js/components/global/splashscreens/AccountSuspended';
import WnlSplashScreenOrderCanceled from 'js/components/global/splashscreens/OrderCanceled';
import WnlSplashScreenUpcomingEdition from 'js/components/global/splashscreens/UpcomingEdition';
import WnlSplashScreenOrderNotPaid from 'js/components/global/splashscreens/OrderNotPaid';
import WnlSplashScreenSubscriptionExpired from 'js/components/global/splashscreens/SubscriptionExpired';
import WnlSplashScreenDefault from 'js/components/global/splashscreens/Default';
import WnlSplashScreenGenericError from 'js/components/global/splashscreens/GenericError';

import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import { getApiUrl } from 'js/utils/env';
import { PRODUCTS_SLUGS } from 'js/consts/products';
import { SUBSCRIPTION_STATUS } from 'js/consts/user';

export default {
	components: {
		WnlMainNav,
		WnlSidenavSlot,
		WnlSplashScreenAccountSuspended,
		WnlSplashScreenUpcomingEdition,
		WnlSplashScreenOrderNotPaid,
		WnlSplashScreenSubscriptionExpired,
		WnlSplashScreenDefault,
		WnlSplashScreenGenericError,
		WnlSplashScreenOrderCanceled
	},
	data() {
		return {
			isLoading: true,
			EXPIRED: SUBSCRIPTION_STATUS.EXPIRED,
			orders: []
		};
	},
	perimeters: [upcomingEditionParticipant],
	computed: {
		...mapGetters([
			'currentUserAccountSuspended',
			'currentUserSubscriptionStatus',
			'currentUserLoadingError',
			'isSidenavVisible',
			'isSidenavMounted',
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
	async mounted() {
		try {
			await this.fetchOrders();
		} catch (e) {
			$wnl.logger.capture(e);
		} finally {
			this.isLoading = false;
		}
	},
	methods: {
		async fetchOrders() {
			const { data: orders } = await axios.get(getApiUrl('users/current/orders/all'));
			this.orders = orders;
		},
	}
};
</script>
