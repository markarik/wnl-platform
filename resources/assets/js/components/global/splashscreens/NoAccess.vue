<template>
	<div>
		<img class="splash-screen-image" :src="countdownImageUrl" alt="Odliczamy dni do kursu">
		<div class="has-text-centered">
			<p class="title is-4">Twoje konto zostało zablokowane</p>
			<p>
				Niestety, Twoje konto zostało zablokowane. 🙁 <br/>
				Sprawdź w zakładce <router-link :to="{name: 'my-orders'}">KONTO > Moje zamówienia</router-link>, czy Twoje zamówienie jest opłacone. <br/>
				W razie pytań pisz do nas na <a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl.</a> 🙂
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
import { mapGetters } from 'vuex';

require('moment-duration-format');

export default {
	computed: {
		...mapGetters('products', ['getCurrentCourse']),
		countdownImageUrl() {
			return getImageUrl('countdown.png');
		},
	},
};
</script>
