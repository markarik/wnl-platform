<template>
	<div>
		<figure class="has-text-centered margin bottom">
			<img :src="imageUrl" alt="" class="onboarding-image">
		</figure>
		<h2 class="title has-text-centered">Gwarancja satysfakcji ⭐️</h2>

		<div class="row margin-top-huge">
			<div class="row-item">
				<span class="row-item-icon">🇵🇱</span>
				<span>Dla osób, które ukończyły studia w języku polskim.</span>
			</div>
			<div class="row-item">
				<span class="row-item-icon">😤</span>
				<span>Jeśli ukończysz kurs w terminie i nie zdasz egzaminu, otrzymasz darmowe przedłużenie uczestnictwa lub zwrot wpłaty!</span>
			</div>
		</div>

		<h3 class="title is-4 has-text-centered margin-top-huge">3 warunki przyznania Gwarancji satysfakcji</h3>
		<p class="has-text-centered margin bottom">Czyli dlaczego warto rozwiązać Wstępny LEK?</p>

		<div class="ordered-item">
			<div class="ordered-item-number">1</div>
			<div>
				<h3 class="title is-4">Rozwiązanie próbnego, Wstępnego LEK-u przed rozpoczęciem pierwszej lekcji</h3>
				<p>Zanim rozpoczniesz naukę - sprawdź swoją wiedzę! To kluczowe dla dalszej pracy. Link do egzaminu znajdziesz na końcu tego samouczka, a później w lekcji Wstępny LEK.</p>
			</div>
		</div>

		<div class="ordered-item">
			<div class="ordered-item-number -purpleSecondary">2</div>
			<div>
				<h3 class="title is-4">Ukończenie kursu najpóźniej dzień przed egzaminem</h3>
				<p>Ukończenie kursu wymaga minimum:</p>
				<div class="content">
					<ul>
						<li>300h aktywności na platformie</li>
						<li>80% ukończonych lekcji</li>
						<li>2500 rozwiązanych pytań zamkniętych</li>
					</ul>
				</div>
				<p>Wszystkie statystyki możesz śledzić w zakładce KONTO > Statystyki.</p>
			</div>
		</div>

		<div class="ordered-item">
			<div class="ordered-item-number -skyBlue">3</div>
			<div>
				<h3 class="title is-4">Rozwiązanie próbnego, Końcowego LEK-u najpóźniej dzień przed egzaminem</h3>
				<p>Egzamin znajdziesz w lekcji Końcowy LEK.</p>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.onboarding-image
		max-width: 200px
</style>

<script>
import { getImageUrl } from 'js/utils/env';

export default {
	data() {
		return {
			imageUrl: getImageUrl('onboarding-screen-satisfaction-guarantee.png'),
		};
	}
};
</script>
