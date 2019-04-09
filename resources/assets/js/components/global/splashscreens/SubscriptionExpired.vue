<template>
	<div>
		<img class="splash-screen-image" :src="imageUrl" :alt="imageAlt">
		<div class="has-text-centered margin top">
			<p class="title is-4">Twoja subskrybcja wygasła.<br>Dziękujemy za wspólną naukę!</p>
			<template v-if="getCurrentCourseProductSignupsOpen">
				<p class="margin vertical">🎁 Otrzymujesz 50% zniżki na każdą kolejną edycję kursu! 🎁</p>
				<p class="margin vertical">Zniżkę automatycznie dodamy do Twojego następnego zamówienia.</p>
				<a target="_blank" :href="signUpLink" class="button is-primary margin vertical">Zapisz się na kolejną edycję</a>
			</template>
			<template v-else>
				<p class="margin vertical">Aktualnie zapisy na kurs są zamknięte.</p>
				<p class="margin vertical">Możemy powiadomić Cię mailowo, gdy zostaną otwarte. 🙂</p>
				<a target="_blank" href="https://wiecejnizlek.pl/newsletter/" class="button is-primary margin vertical">Dołącz do newslettera</a>
			</template>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.splash-screen-image
		max-width: 250px
</style>

<script>
import {getImageUrl, getUrl} from 'js/utils/env';
import {mapGetters} from 'vuex';

export default {
	computed: {
		...mapGetters('products', ['getCurrentCourseProductSignupsOpen']),
		imageAlt() {
			return this.getCurrentCourseProductSignupsOpen ? 'Zniżka 50%' : 'Logo kursu';
		},
		imageUrl() {
			return this.getCurrentCourseProductSignupsOpen ?
				getImageUrl('coupon-participant.png') :
				window.$wnl.course.productLogoWithStudents;
		},
		signUpLink() {
			return getUrl('payment/account');
		},
	},
};
</script>
