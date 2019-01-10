<template>
	<aside class="menu">
		<p class="menu-label">
			Lekcje
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'course-edit', params: {id: 1} }">Edycja kursu</router-link></li>
			<li><router-link :to="{ name: 'groups' }">Edycja grup lekcji</router-link></li>
			<li><router-link :to="{ name: 'lessons' }">Edycja lekcji</router-link></li>
		</ul>

		<p class="menu-label">
			Prezentacje
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'slides' }">Edytuj Slajd</router-link></li>
			<li><router-link :to="{ name: 'add-slide' }">Dodaj Slajd</router-link></li>
			<li><router-link :to="{ name: 'charts' }">Diagramy</router-link></li>
			<li><router-link :to="{ name: 'annotations' }">Przypisy</router-link></li>
		</ul>

		<p class="menu-label">Tagi i Taksonomie</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'tags' }">Edycja tagów</router-link></li>
			<li><router-link :to="{ name: 'taxonomies' }">Edycja taksonomii</router-link></li>
		</ul>

		<p class="menu-label">
			Pytania
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'quizes' }">Edytuj pytania zamknięte</router-link></li>
		</ul>
		<p class="menu-label">
			Użytkownicy
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'users' }">Lista użytkowników</router-link></li>
		</ul>
		<p class="menu-label">
			Powtórki
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'flashcards-sets' }">Edycja zestawu pytań</router-link></li>
			<li><router-link :to="{ name: 'flashcards' }">Edycja pytań</router-link></li>
		</ul>
		<p class="menu-label">Newsy</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'dashboard-news' }">Dashboard newsy</router-link></li>
		</ul>
		<p class="menu-label">Zamówienia</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'orders' }">Lista zamówień</router-link></li>
		</ul>
	</aside>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
export default {
	name: 'Sidenav'
};
</script>
