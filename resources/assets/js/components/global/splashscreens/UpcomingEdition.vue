<template>
	<div class="splash-screen">
		<img class="splash-screen-image" :src="countdownImageUrl" alt="Odliczamy dni do kursu">
		<div class="splash-screen-countdown">
			<p class="title is-4">Twoja przygoda z kursem zacznie się już za:</p>
			<template v-if="diff > 0">
				{{daysLeft}}dni
				{{hoursLeft}}godz
				{{minutesLeft}}min
			</template>
			<p class="info">
				Twoje subskrypcja będzie aktywna do {{endDate}}
			</p>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.splash-screen
		align-items: center
		display: flex
		flex: 1 0 auto
		flex-direction: column
		height: 100%
		justify-content: center
		min-height: 100%
		width: 100%

	.splash-screen-image
		max-width: 240px

	.splash-screen-countdown
		font-size: $font-size-plus-7
		font-weight: $font-weight-black
		line-height: $line-height-plus
		text-align: center

		.info
			font-size: $font-size-base
			font-weight: $font-weight-regular
			line-height: $line-height-base
			margin: $margin-base

</style>

<script>
import moment from 'moment';
import { mapGetters } from 'vuex';

export default {
	data() {
		return {
			diff: 0,
			intervalId: null
		};
	},
	computed: {
		...mapGetters(['currentUserSubscriptionDates', 'currentUserAccountSuspended']),
		countdownImageUrl() {
			return window.$wnl.course.productLogoBig;
		},
		daysLeft() {
			return Math.floor(this.diff / 1440);
		},
		hoursLeft() {
			return Math.floor(this.diff / 60) - (this.daysLeft * 24);
		},
		minutesLeft() {
			return this.diff - this.daysLeft * 1440 - this.hoursLeft * 60;
		},
		endDate() {
			return moment(new Date(this.currentUserSubscriptionDates.max * 1000)).format('LL');
		}
	},
	created() {
		const accessStart = new Date(this.currentUserSubscriptionDates.min * 1000);
		const now = new Date();
		this.diff = moment(accessStart).diff(now, 'minutes');
		this.intervalId = setInterval(() => {
			this.diff = this.diff - 1;
		}, 60000);
	},
	beforeDestroy() {
		clearInterval(this.intervalId);
	}
};
</script>
