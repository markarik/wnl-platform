<template>
	<wnl-sidenav-slot
		:is-visible="isSidenavVisible"
		:is-detached="!isSidenavMounted"
	>
		<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
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
import { mapGetters } from 'vuex';

import MainNav from 'js/components/MainNav';
import Sidenav from 'js/components/global/Sidenav';
import SidenavSlot from 'js/components/global/SidenavSlot';

import navigation from 'js/services/navigation';

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
					text: this.$t('questions.nav.dashboard'),
					routeName: 'questions-dashboard',
					iconClass: 'fa-home',
					iconTilte: this.$t('questions.nav.dashboard'),
					itemClass: 'has-icon',
				}),
				navigation.composeItem({
					text: this.$t('questions.nav.solving'),
					routeName: 'questions-list',
					iconClass: 'fa-check',
					iconTilte: this.$t('questions.nav.solving'),
					itemClass: 'has-icon',
				}),
				// navigation.composeItem({
				// 	text: this.$t('questions.nav.stats'),
				// 	routeName: 'questions-stats',
				// 	iconClass: 'fa-bar-chart',
				// 	iconTilte: this.$t('questions.nav.stats'),
				// 	itemClass: 'has-icon',
				// }),
				navigation.composeItem({
					text: this.$t('questions.nav.planner'),
					routeName: 'questions-planner',
					iconClass: 'fa-calendar',
					iconTilte: this.$t('questions.nav.planner'),
					itemClass: 'has-icon',
				}),
			];
		}
	},
};
</script>
