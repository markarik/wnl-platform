<template>
	<div>
		<img class="splash-screen-image" :src="countdownImageUrl" alt="Logo kursu">
		<div class="has-text-centered">
			<p class="title is-4">Twoje zamówienie oczekuje na płatność</p>
			<p class="margin vertical">
				Na płatność masz <strong>7 dni</strong> od złożenia zamówienia.
			</p>
			<p class="margin vertical text-dimmed" v-if="currentProductAccessStartDateIsPast">Dostęp do kursu otrzymasz od razu po dokonaniu płatności.</p>
			<p class="margin vertical text-dimmed" v-else>Dostęp do kursu otrzymasz od {{currentProductAccessStartDate}}.</p>
			<p class="margin vertical">
				<a :href="paymentUrl" class="button is-primary">
					Opłać zamówienie
				</a>
			</p>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.splash-screen-image
		max-width: 240px
		margin-bottom: $margin-big
</style>

<script>
import moment from 'moment';
import { getUrl } from 'js/utils/env';
import { mapGetters } from 'vuex';

require('moment-duration-format');

export default {
	computed: {
		...mapGetters('products', ['getCurrentCourse']),
		countdownImageUrl() {
			return window.$wnl.course.productLogoBig;
		},
		paymentUrl() {
			return getUrl('payment/account');
		},
		currentProductAccessStartDateIsPast() {
			return this.getCurrentCourse && moment(this.getCurrentCourse.access_start * 1000).isBefore();
		},
		currentProductAccessStartDate() {
			return this.getCurrentCourse && moment(this.getCurrentCourse.access_start * 1000).format('LL');
		},
	},
};
</script>
