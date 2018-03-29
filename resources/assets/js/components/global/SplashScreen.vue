<template>
	<div class="splash-screen scrollable-main-container">
		<img class="splash-screen-image" :src="countdownImageUrl" alt="Odliczamy dni do kursu">
		<div class="splash-screen-countdown" v-if="$upcomingEditionParticipant.isAllowed('access')">
			<p class="title is-4">Odliczamy dni do początku kursu!</p>
			&nbsp;<span v-if="loaded">{{ timeLeft.value }}</span>
		</div>
		<div v-else>
			<p class="title is-4">Druga edycja kursu "Więcej niż LEK" oficjalnie wystartowała! </p>
			<p class="has-text-centered">Widzisz ten ekran, ponieważ nie posiadasz dostępu do drugiej edycji.<br>
			W razie, gdyby okazało się to nieporozumieniem, napisz do nas na info@wiecejnizlek.pl albo na
				<a href="https://facebook.com/wiecejnizlek">facebooku</a>.</p>
		</div>
		<a href="http://demo.wiecejnizlek.pl" class="button is-primary is-outlined">
			Zobacz wersję demonstracyjną platformy
		</a>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
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
		max-width: 400px
		padding: 0 20px

	.splash-screen-countdown
		font-size: 4em
		font-weight: 900
		line-height: 2em
		text-align: center

	.button
		display: block
</style>

<script>
	import moment from 'moment'
	import { getImageUrl } from 'js/utils/env'
	import { set } from 'vue'
	import { mapGetters } from 'vuex'
	import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant'

	require('moment-duration-format')

	export default {
		name: 'SplashScreen',
		perimeters: [upcomingEditionParticipant],
		computed: {
			...mapGetters(['currentUserSubscriptionDates']),
			countdownImageUrl() {
				return getImageUrl('countdown.png')
			}
		},
		data: () => {
			return {
				loaded: false,
				timeLeft: {
					value: 0
			 	}
			}
		},
		methods: {
			getTimeLeft() {
				const theDate = new Date(this.currentUserSubscriptionDates.min.date)
				return moment.duration(moment(theDate).diff(moment(), 'seconds'), 'seconds').format('d[d] h[h] m[m] s[s]')
			},
			setTimeLeft() {
				set(this.timeLeft, 'value', this.getTimeLeft())
				this.loaded = true
			},
		},
		mounted() {
			this.$upcomingEditionParticipant.isAllowed('access') && window.setInterval(this.setTimeLeft, 1000)
		}
	}
</script>
