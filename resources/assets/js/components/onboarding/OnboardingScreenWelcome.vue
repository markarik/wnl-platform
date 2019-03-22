<template>
	<div>
		<img :src="imageUrl">
		<h2>Cześć {{currentUserName}} 👋</h2>
		<p>Zanim rozpoczniesz naukę, poznasz najważniejsze funkcje platformy i kursu ⭐</p>
		<div>
			<div>
				<span>⏰</span>
				<p>Wprowadzenie zajmie Tobie około 10min.</p>
			</div>
			<div>
				<span>🔁</span>
				<p>Wprowadzenie możesz zamknąc w dowolnym momencie. Bedzie na Ciebie czekać.</p>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
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
