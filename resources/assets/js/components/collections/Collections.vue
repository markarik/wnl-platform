<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="wnl-sidenav">
				<wnl-sidenav :items="items" :breadcrumbs="breadcrumbs"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main" v-bind:class="{'full-width': isMobileProfile}">
			<router-view></router-view>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		padding: $margin-small

	.wnl-middle
		padding: $margin-small $margin-base
</style>

<script>
	import { mapGetters } from 'vuex'

	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'

	export default {
		props: ['view'],
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile']),
			items() {
				let items = [
					{
						text: 'Kolekcje',
						itemClass: 'heading small',
					},
					{
						text: 'Slajdy',
						itemClass: 'has-icon',
						to: {
							name: 'collection-slides',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-television',
						iconTitle: 'Twoja kolekcja slajdów',
					},
					{
						text: 'Pytania i odpowiedzi',
						itemClass: 'has-icon',
						to: {
							name: 'collection-qna',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-th-list',
						iconTitle: 'Twoja kolekcja pytań i odpowiedzi',
					},
					{
						text: 'Pytania kontrolne',
						itemClass: 'has-icon',
						to: {
							name: 'collection-quiz',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-question-circle-o',
						iconTitle: 'Twoja kolekcja pytań i odpowiedzi',
					},
				]

				if (this.isProduction) {
					items.push({
						text: 'Kiedy kurs?',
						itemClass: 'has-icon',
						to: {
							name: 'countdown',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-question',
						iconTitle: 'Kiedy kurs?',
					})
				}

				return items
			},
			breadcrumbs() {
				let breadcrumbs = [
					{
						text: 'Plan lekcji',
						itemClass: 'has-icon',
						to: {
							name: 'dashboard',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-home',
						iconTitle: 'Plan lekcji',
					},
				]

				return breadcrumbs
			}
		},
		methods: {
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav
		},
		// mounted() { this.goToDefaultRoute() }
	}
</script>
