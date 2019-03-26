<template>
	<div class="has-text-centered">
		<img :src="imageUrl">
		<h2 class="title">Cześć {{currentUserName}} 👋</h2>
		<p class="title is-5">Zanim rozpoczniesz naukę, poznasz najważniejsze funkcje platformy i kursu ⭐</p>
		<div class="row">
			<div class="row-item">
				<span class="margin right">⏰</span>
				<span>Wprowadzenie zajmie Tobie około 10min.</span>
			</div>
			<div class="row-item">
				<span class="margin right">🔁</span>
				<span>Wprowadzenie możesz zamknąc w dowolnym momencie. Bedzie na Ciebie czekać.</span>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
import {mapGetters} from 'vuex';

import {getImageUrl} from 'js/utils/env';

export default {
	components: {

	},
	computed: {
		...mapGetters([
			'currentUserName'
		]),
		imageUrl() {
			return getImageUrl('onboarding-screen-welcome.png');
		}
	},
};
</script>
