<template>
	<div>
		<figure class="has-text-centered margin bottom">
		<img :src="imageUrl" alt="" class="onboarding-image">
		</figure>
		<h2 class="title has-text-centered">Witaj na kursie do LEK!️</h2>

		<div class="ordered-item">
			<div class="ordered-item-number">1</div>
			<div>
				<h3 class="title is-4">Rozwiąż Wstępny LEK</h3>
				<div class="content">
					<ul>
						<li>Rozwiąż Wstępny LEK przed pierwszą lekcją, jest to pierwszy z warunków uzyskania Gwarancji Satysfakcji.</li>
						<li>Gwarantujemy, że rozwiązanie wstępnego egzaminu wpłynie pozytywnie na Twoją naukę – daj sobie 3-4 godziny na to wyzwanie!</li>
						<li>Jeżeli realizujesz aktualnie indywidualny plan pracy w ramach przedłużonego dostępu, możesz pominąć wstępny LEK jeśli masz go rozwiązanego.</li>
						<li>Wstępny LEK będzie dla Ciebie zawsze otwarty, znajdziesz go w module WSTĘPNY LEK.</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="ordered-item">
			<div class="ordered-item-number -purpleSecondary">2</div>
			<div>
				<h3 class="title is-4">Zapoznaj się z modułami Wstęp do kursu i Efektywna nauka</h3>
				<p>Ukończ moduły "Wstęp do kursu" i "Efektywna nauka" a zrozumiesz metodykę pracy na kursie i oszczędzisz wiele czasu na nauce!</p>
			</div>
		</div>

		<div class="ordered-item">
			<div class="ordered-item-number -skyBlue">3</div>
			<div>
				<h3 class="title is-4">Zacznij pierwszą lekcję!</h3>
				<p>Lekcje będą otwierały się zgodnie z wybranym przez Ciebie planem - zawsze może go zmienić w zakładce KONTO - Plan pracy.</p>
			</div>
		</div>

		<p class="title is-5 has-text-centered">Przystanek pierwszy - Wstępny LEK!</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.onboarding-image
		max-width: 500px
</style>

<script>
import {getImageUrl} from 'js/utils/env';

export default {
	data() {
		return {
			imageUrl: getImageUrl('happy-hands.svg'),
		};
	}
};
</script>
