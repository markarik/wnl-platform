<template>
	<div>
		<img class="splash-screen-image" :src="logoImageUrl" alt="Logo kursu">
		<div class="has-text-centered">
			<p class="title is-4">Twoje zamówienie zostało anulowane ze względu na brak płatności.</p>
			<p class="text-dimmed">
				Aby uzyskać dostęp do kursu złóż i opłać nowe zamówienie.
			</p>
			<p>
				<a href="/payment" class="button is-primary margin top">Złóż nowe zamówienie</a>
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
