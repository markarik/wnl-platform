<template>
	<div class="splash-screen scrollable-main-container">
		<img class="splash-screen-image" :src="countdownImageUrl" alt="Odliczamy dni do kursu">
		<div class="splash-screen-countdown" v-if="$upcomingEditionParticipant.isAllowed('access')">
			<p class="title is-4">Dostęp do kursu uzyskasz już {{startDate}}!</p>
			<p class="info"></p>
			<p class="info">
				Twoje zamówienia znajdziesz w zakładce - <router-link :to="{name: 'my-orders'}">KONTO > Twoje zamówienia</router-link>.
			</p>
		</div>
		<div class="has-text-centered" v-else>
			<p class="title is-4">Dziękujemy za wspólną naukę!</p>
			<p>Widzisz ten ekran, ponieważ nie posiadasz już dostępu do kursu. 🙂<br>
			W razie, gdyby okazało się to nieporozumieniem, napisz do nas na info@wiecejnizlek.pl albo na
				<a href="https://facebook.com/wiecejnizlek">facebooku</a>.
			</p>
			<p class="margin vertical">
				<a href="http://wiecejnizlek.pl/zapisy" class="button is-primary is-outlined">
					Sprawdź zapisy na kolejną edycję
				</a>
			</p>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
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
		max-width: 400px
		padding: 0 20px

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
			},
			startDate() {
				return moment(new Date(this.currentUserSubscriptionDates.min * 1000)).format('LL')
			},
		},
	}
</script>
