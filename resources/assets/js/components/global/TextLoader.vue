<template>
	<div class="text-loader">
		<span class="icon">
			<i class="fa fa-spin fa-circle-o-notch"></i>
		</span>
		<slot>Momencik, ładuję się...</slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.text-loader
		text-align: center

		.icon
			margin-right: $margin-tiny
</style>

<script>
	export default {
		name: 'TextLoader',
	}
</script>
