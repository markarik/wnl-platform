<template>
	<div class="has-text-centered">
		<img :src="imageUrl" alt="" class="onboarding-image margin bottom">
		<h2 class="title">Gwarancja satysfakcji ⭐️</h2>
		<div class="row">
			<div class="row-item">
				<span>🇵🇱</span>
				<span>Dla osób, które ukończyły studia w języku polskim.</span>
			</div>
			<div class="row-item">
				<span>😤</span>
				<span>Jeśli ukończysz kurs w terminie i nie zdasz egzaminu, otrzymasz darmowe przedłużenie uczestnictwa lub zwrot wpłaty!</span>
			</div>
		</div>

		<h3 class="title is-3">3 warunki przyznania Gwarancji satysfakcji</h3>
		<p>Czyli dlaczego warto rozwiązać Wstępny LEK?</p>

		<h3>1. Rozwiązanie próbnego, Wstępnego LEK-u przed rozpoczęciem pierwszej lekcji</h3>
		<p>Zanim siądziesz do nauki - sprawdź swoją wiedzę! To kluczowe dla dalszej pracy. Link do egzaminu znajdziesz na końcu tego samouczka, a później w lekcji Wstępny LEK.</p>

		<h3>2. Ukończenie kursu najpóźniej dzień przed egzaminem</h3>
		<p>Ukończenie kursu wymaga minimum:</p>
		<ul>
			<li>300h aktywności na platformie</li>
			<li>80% ukończonych lekcji</li>
			<li>2500 rozwiązanych pytań zamkniętych</li>
		</ul>
		<p>Wszystkie statystyki możesz śledzić w zakładce KONTO > Statystyki.</p>

		<h3>3. Rozwiązanie próbnego, Końcowego LEK-u najpóźniej dzień przed egzaminem</h3>
		<p>Egzamin znajdziesz w lekcji Końcowy LEK.</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.onboarding-image
		max-width: 200px
</style>

<script>
import {getImageUrl} from 'js/utils/env';

export default {
	data() {
		return {
			imageUrl: getImageUrl('onboarding-screen-satisfaction-guarantee.png'),
		};
	}
};
</script>
