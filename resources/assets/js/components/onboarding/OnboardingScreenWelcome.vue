<template>
	<div>
		<figure class="has-text-centered margin bottom">
			<img :src="imageUrl" alt="" class="onboarding-image">
		</figure>
		<h2 class="title has-text-centered">Cześć {{currentUserName}} 👋</h2>
		<p class="title is-5 has-text-centered">Zanim rozpoczniesz naukę, poznasz najważniejsze funkcje platformy i kursu ⭐</p>
		<div class="row">
			<div class="row-item">
				<span class="row-item-icon">⏰</span>
				<span>Wprowadzenie zajmie Tobie około 10min.</span>
			</div>
			<div class="row-item">
				<span class="row-item-icon">🔁</span>
				<span>Wprowadzenie możesz zamknąc w dowolnym momencie. Bedzie na Ciebie czekać.</span>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.onboarding-image
		max-width: 274px
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
