<template>
	<div class="splash-screen scrollable-main-container">
		<p class="title is-4">Do drugiej edycji kursu "Więcej niż LEK" pozostało jeszcze</p>
		<p class="splash-screen-countdown">
			&nbsp;<span v-if="loaded">{{ timeLeft.value }}</span>
		</p>
		<img class="splash-screen-image" :src="countdownImageUrl" alt="Odliczamy dni do kursu">
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
		// text-transform: uppercase

	.button
		display: block
</style>

<script>
	import moment from 'moment'
	import { getImageUrl } from 'js/utils/env'
	import { set } from 'vue'

	require('moment-duration-format')

	const theDate = "2017-11-04"

	export default {
		name: 'SplashScreen',
		computed: {
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
				return moment.duration(moment(theDate).diff(moment(), 'seconds'), 'seconds').format('d[d] h[h] m[m] s[s]')
			},
			setTimeLeft() {
				set(this.timeLeft, 'value', this.getTimeLeft())
				this.loaded = true
			},
		},
		mounted() {
			window.setInterval(this.setTimeLeft, 1000)
		}
	}
</script>
