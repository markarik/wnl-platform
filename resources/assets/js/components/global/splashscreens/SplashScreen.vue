<template>
	<div class="splash-screen__container">
		<div class="splash-screen__content scrollable-main-container">
			<wnl-text-loader v-if="isLoading"></wnl-text-loader>
			<template v-else>
				<wnl-no-access v-if="currentUserAccountSuspended"/>
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

import WnlNoAccess from 'js/components/global/splashscreens/NoAccess';
import WnlUpcomingEdition from 'js/components/global/splashscreens/UpcomingEdition';
import WnlOrderNotPaid from 'js/components/global/splashscreens/OrderNotPaid';
import WnlDefaultSplashScreen from 'js/components/global/splashscreens/Default';

import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import {getApiUrl} from 'js/utils/env';
import {PRODUCTS_SLUGS} from 'js/consts/products';

export default {
	data() {
		return {
			latestCourseWaitingForPayment: false,
			isLoading: true,
		};
	},
	components: { WnlNoAccess, WnlUpcomingEdition, WnlOrderNotPaid, WnlDefaultSplashScreen },
	perimeters: [upcomingEditionParticipant],
	computed: {
		...mapGetters(['currentUserAccountSuspended']),
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
