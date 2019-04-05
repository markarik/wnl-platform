<template>
	<div>
		<img class="splash-screen-image" :src="countdownImageUrl" alt="">
		<div class="has-text-centered">
			<p class="title is-4">W tym momencie nie posiadasz dostępu do kursu</p>
			<p>Widzisz ten ekran ponieważ nie opłaciłeś zamówienia w terminie lub jesteś uczestnikiem poprzedniej edycji, która dobiegła już końca. 🙂<br>
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

	.splash-screen-image
		max-width: 400px
		padding: 0 20px
</style>

<script>
import { getImageUrl, getUrl } from 'js/utils/env';

export default {
	data() {
		return {
			paymentUrl: getUrl('payment/account'),
		};
	},
	computed: {
		countdownImageUrl() {
			return getImageUrl('countdown.png');
		},
	},
};
</script>
