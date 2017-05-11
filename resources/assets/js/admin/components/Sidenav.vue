<template>
	<aside class="menu">
		<p class="menu-label">
			Lekcje
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'lessons' }">Edycja lekcji</router-link></li>
		</ul>
	</aside>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
	export default {
		name: 'Sidenav',
	}
</script>
