<template>
	<div>
		<img class="splash-screen-image" :src="logoImageUrl" alt="Logo kursu">
		<div class="has-text-centered">
			<p class="title is-4">Twoje konto zostało zablokowane... 🙁</p>
			<p v-if="instalmentsNotPaid" class="text-dimmed">
				Zaległe raty możesz opłacić w zakładce <router-link :to="{name: 'my-orders'}">KONTO > Twoje zamówienia.</router-link>
			</p>
			<p v-else class="text-dimmed">
				Aby uzyskać więcej informacji napisz do nas na Messengerze
				lub wyślij maila na adres: <a href="mailto:info@wiecejnizlek.pl.">info@wiecejnizlek.pl.</a>
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
	props: {
		instalmentsNotPaid: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		logoImageUrl() {
			return window.$wnl.course.productLogoWithStudents;
		},
	},
};
</script>
