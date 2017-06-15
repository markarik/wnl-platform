<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="wnl-sidenav">
				<wnl-sidenav :items="items"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main" v-bind:class="{'full-width': isMobileProfile}" v-if="!isLoading">
			<router-view></router-view>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		padding: $margin-small

	.wnl-middle
		border-right: $border-light-gray
		padding: $margin-small $margin-base
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'

	export default {
		props: ['view'],
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile']),
			...mapGetters('collections', ['isLoading']),
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

				return items
			}
		},
		methods: {
			...mapActions('collections', ['fetchReactions']),
		},
		mounted() {
			this.fetchReactions()
		}
	}
</script>
