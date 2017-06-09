<template lang="html">
	<div class="nav-container">
		<label class="label" for="wnl-slideshow-navigation-">
			Przejdź do slajdu
		</label>
		<input class="input" type="text" size="3" maxlength="3"
			v-model:value="slideNumber"
			@keyup.enter="goToSlide">
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.nav-container
		align-items: center
		display: flex

	.label
		flex: 1 0 auto
		font-size: $font-size-minus-1
		font-weight: $font-weight-regular
		margin: 0 5px
</style>

<script>
	export default {
		name: 'SlideshowNavigation',
		data() {
			return {
				slideNumber: 1
			}
		},
		methods: {
			goToSlide() {
				this.$router.replace({ name: 'screens', params: { slide: this.slideNumber } })
			}
		}
	}
</script>
