<template>
	<div class="splash-screen__container">
		<div class="splash-screen__content scrollable-main-container">
			<wnl-text-loader v-if="isLoading"></wnl-text-loader>
			<template v-else>
				<wnl-splash-screen-generic-error v-if="currentUserLoadingError"/>
				<wnl-splash-screen-no-access v-else-if="currentUserAccountSuspended"/>
				<wnl-splash-screen-upcoming-edition v-else-if="$upcomingEditionParticipant.isAllowed('access')"/>
				<wnl-splash-screen-order-not-paid v-else-if="latestCourseWaitingForPayment"/>
				<wnl-splash-screen-subscription-expired v-else-if="currentUserSubscriptionStatus === EXPIRED"/>
				<wnl-splash-screen-default v-else/>
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

import WnlSplashScreenNoAccess from 'js/components/global/splashscreens/NoAccess';
import WnlSplashScreenUpcomingEdition from 'js/components/global/splashscreens/UpcomingEdition';
import WnlSplashScreenOrderNotPaid from 'js/components/global/splashscreens/OrderNotPaid';
import WnlSplashScreenSubscriptionExpired from 'js/components/global/splashscreens/SubscriptionExpired';
import WnlSplashScreenDefault from 'js/components/global/splashscreens/Default';
import WnlSplashScreenGenericError from 'js/components/global/splashscreens/GenericError';

import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import {getApiUrl} from 'js/utils/env';
import {PRODUCTS_SLUGS} from 'js/consts/products';
import {SUBSCRIPTION_STATUS} from 'js/consts/user';

export default {
	data() {
		return {
			latestCourseWaitingForPayment: false,
			isLoading: true,
			EXPIRED: SUBSCRIPTION_STATUS.EXPIRED,
		};
	},
	components: {
		WnlSplashScreenNoAccess,
		WnlSplashScreenUpcomingEdition,
		WnlSplashScreenOrderNotPaid,
		WnlSplashScreenSubscriptionExpired,
		WnlSplashScreenDefault,
		WnlSplashScreenGenericError,
	},
	perimeters: [upcomingEditionParticipant],
	computed: {
		...mapGetters([
			'currentUserAccountSuspended',
			'currentUserSubscriptionStatus',
			'currentUserLoadingError',
		]),
	},
	methods: {
		async getIsLatestCourseWaitingForPayment() {
			const {data: orders} = await axios.get(getApiUrl('orders/all'));
			const courseOrders = orders.filter(order => order.product.slug === PRODUCTS_SLUGS.SLUG_ONLINE);

			if (courseOrders.length === 0) {
				return false;
			}

			const maxCourseProductId = courseOrders.reduce((max, currentOrder) => currentOrder.product.id > max ? currentOrder.product.id : max, null);

			return !courseOrders.filter(order => order.product.id === maxCourseProductId && !order.canceled)
				.some(order => order.paid);
		},
	},
	async mounted() {
		try {
			this.latestCourseWaitingForPayment = await this.getIsLatestCourseWaitingForPayment();
		} catch (e) {
			$wnl.logger.capture(e);
		} finally {
			this.isLoading = false;
		}
	}
};
</script>
