<template>
	<div class="container">
		<p>Screen: {{ screenId }}</p>
		<p>Slide: {{ slide }}</p>
	</div>
</template>

<style lang="sass">
</style>

<script>
	export default {
		name: 'Screen',
		props: ['screenId', 'slide']
	}
</script>
