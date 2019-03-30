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
		<div class="has-text-centered" v-else-if="currentUserAccountSuspended">
			<p class="title is-4">Twoje konto zostało zablokowane</p>
			<p>
				Niestety, Twoje konto zostało zablokowane. 🙁 <br/>
				Sprawdź w zakładce <router-link :to="{name: 'my-orders'}">KONTO > Moje zamówienia</router-link>, czy Twoje zamówienie jest opłacone. <br/>
				W razie pytań pisz do nas na <a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl.</a> 🙂
			</p>
		</div>
		<div class="has-text-centered" v-else>
			<p class="title is-4">W tym momencie nie posiadasz dostępu do kursu</p>
			<p>Widzisz ten ekran ponieważ Twoje zamówienie oczekuje na zaksięgowanie wpłaty, lub jesteś uczestnikiem poprzedniej edycji, która dobiegła już końca. 🙂<br>
			W razie, gdyby okazało się to nieporozumieniem, napisz do nas na info@wiecejnizlek.pl albo na
				<a href="https://facebook.com/wiecejnizlek">facebooku</a>.
			</p>
			<p class="margin vertical">
				<a :href="paymentUrl" class="button is-primary is-outlined">
					Zapisz się na najbliższą edycję
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
import moment from 'moment';
import { getImageUrl, getUrl } from 'js/utils/env';
import { set } from 'vue';
import { mapGetters } from 'vuex';
import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';

require('moment-duration-format');

export default {
	name: 'SplashScreen',
	perimeters: [upcomingEditionParticipant],
	computed: {
		...mapGetters(['currentUserSubscriptionDates', 'currentUserAccountSuspended']),
		countdownImageUrl() {
			return getImageUrl('countdown.png');
		},
		startDate() {
			return moment(new Date(this.currentUserSubscriptionDates.min * 1000)).format('LL');
		},
		paymentUrl() {
			return getUrl('payment/account');
		},
	},
};
</script>
