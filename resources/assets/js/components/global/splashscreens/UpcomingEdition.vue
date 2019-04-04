<template>
	<div>
		<img class="splash-screen-image" :src="countdownImageUrl" alt="Odliczamy dni do kursu">
		<div v-if="diff > 0">
			<p class="title is-4">Twoja przygoda z kursem zacznie się już za:</p>
			<div class="splash-screen-counter">
				<div class="splash-screen-counter__item">
					<span>{{daysLeft}}</span>
					<span class="text-dimmed">dni</span>
				</div>
				<div class="splash-screen-counter__item">
					<span>{{hoursLeft}}</span>
					<span class="text-dimmed">godz</span>
				</div>
				<div class="splash-screen-counter__item">
					<span>{{minutesLeft}}</span>
					<span class="text-dimmed">min</span>
				</div>
				<div class="splash-screen-counter__item -small">
					🚀
				</div>
			</div>
			<p class="splash-screen__info text-dimmed">
				Twoje subskrypcja będzie aktywna do {{endDate}}
			</p>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.splash-screen-image
		max-width: 240px

	.splash-screen-counter
		display: flex
		justify-content: center
		margin-bottom: $margin-huge

		&__item
			margin-right: $margin-small
			font-size: $font-size-plus-2
			font-weight: bold

			@media #{$media-query-tablet}
				font-size: $font-size-plus-4

			&.-small
				font-size: $font-size-plus-1

				@media #{$media-query-tablet}
					font-size: $font-size-plus-2

			&:last-child
				margin-right: 0

	.splash-screen
		&__info
			font-size: $font-size-plus-1


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
