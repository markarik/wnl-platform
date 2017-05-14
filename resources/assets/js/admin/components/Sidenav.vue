<template>
	<aside class="menu">
		<p class="menu-label">
			Lekcje
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'lessons' }">Edycja lekcji</router-link></li>
		</ul>

		<p class="menu-label">
			Prezentacje
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'slides' }">Edycja slajdu</router-link></li>
		</ul>

		<p class="menu-label">
			Pytania
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'quizes' }">Edytuj zestaw pytań</router-link></li>
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
