<template>
	<div>
		<wnl-text-loader v-if="updateInProgress"></wnl-text-loader>
		<template v-else>
			<img
				class="splash-screen-image"
				:src="logoImageUrl"
				alt="Logo kursu"
			>
			<div v-if="diff >= 0">
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
				<!-- TODO PLAT-1201 clean up and do it correctly -->
				<p v-if="courseSlug === 'ldek'" class="splash-screen__info text-dimmed">
					Album map myśli wyślemy do Ciebie w 2. połowie maja.
				</p>
				<p class="splash-screen__info text-dimmed">
					Twoja subskrypcja będzie aktywna do {{endDate}}.
				</p>
			</div>
		</template>
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
import { mapGetters, mapActions } from 'vuex';

export default {
	data() {
		return {
			diff: 0,
			intervalId: null,
			updateInProgress: false
		};
	},
	computed: {
		...mapGetters('course', ['courseSlug']),
		...mapGetters(['currentUserSubscriptionDates', 'currentUserAccountSuspended']),
		logoImageUrl() {
			return window.$wnl.course.productLogoWithStudents;
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
	methods: {
		...mapActions([
			'fetchUserSubscription',
			'addAutoDismissableAlert'
		]),
		...mapActions('course', { courseSetup: 'setup' }),
	},
	created() {
		const accessStart = new Date(this.currentUserSubscriptionDates.min * 1000);
		const now = new Date();
		this.diff = moment(accessStart).diff(now, 'minutes');
		this.intervalId = setInterval(() => {
			this.diff = this.diff - 1;
		}, 30000);
	},
	beforeDestroy() {
		clearInterval(this.intervalId);
	},
	watch: {
		async diff() {
			if (this.diff < 0 && !this.updateInProgress) {
				this.updateInProgress = true;
				try {
					await Promise.all([
						this.fetchUserSubscription(),
						this.courseSetup(),
						this.$socketChatSetup(),
					]);
					this.$router.push('/');
				} catch (e) {
					$wnl.logger.error(e);
					this.addAutoDismissableAlert({
						text: 'Nie udało nam się pobrać danych na temat Twojego dostępu. Odśwież stronę :)',
						type: 'error'
					});
				} finally {
					this.updateInProgress = false;
				}
			}
		}
	}
};
</script>
