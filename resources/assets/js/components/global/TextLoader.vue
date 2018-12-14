<template>
	<div class="text-loader">
		<span class="loader"></span>
		<slot>Momencik, ładuję dane...</slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.text-loader
		align-items: center
		color: $color-background-gray
		display: flex
		justify-content: center
		text-align: center

	.loader
		margin-right: $margin-small
</style>

<script>
export default {
	name: 'TextLoader',
};
</script>
