<template>
	<wnl-sidenav-slot
		:isVisible="isSidenavVisible"
		:isDetached="!isSidenavMounted"
	>
		<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
		<aside class="sidenav-aside">
			<wnl-sidenav :items="navigationItems"></wnl-sidenav>
		</aside>
	</wnl-sidenav-slot>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.sidenav-aside
		flex: 1
		min-width: $sidenav-min-width
		overflow: auto
		padding: 7px 0
		width: $sidenav-width
</style>

<script>
	import {mapGetters} from 'vuex'

	import MainNav from 'js/components/MainNav'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	import navigation from 'js/services/navigation'

	export default {
		name: 'QuestionsNavigation',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible']),
			navigationItems() {
				return [
					navigation.composeItem({
						text: 'Pytania',
						itemClass: 'heading small',
					}),
					navigation.composeItem({
						text: 'Dashboard',
						routeName: 'questions-dashboard',
						iconClass: 'fa-home',
						iconTilte: 'Dashboard',
						itemClass: 'has-icon',
					}),
					navigation.composeItem({
						text: 'Rozwiązywanie pytań',
						routeName: 'questions-list',
						iconClass: 'fa-check',
						iconTilte: 'Rozwiązywanie pytań',
						itemClass: 'has-icon',
					}),
					navigation.composeItem({
						text: 'Statystyki',
						routeName: 'questions-stats',
						iconClass: 'fa-bar-chart',
						iconTilte: 'Statystyki',
						itemClass: 'has-icon',
					}),
				]
			}
		},
	}
</script>
