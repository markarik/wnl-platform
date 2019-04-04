<template>
	<div>
		<img class="splash-screen-image" :src="logoImageUrl" alt="Logo kursu">
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
export default {
	computed: {
		logoImageUrl() {
			return window.$wnl.course.productLogoWithStudents;
		},
	},
};
</script>
