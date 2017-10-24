<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside myself-sidenav">
				<wnl-sidenav :items="items"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="scrollable-main-container wnl-main" :class="{'full-width': isMobileProfile, 'mobile-main': isMobileProfile}">
			<router-view
				@userDataLoaded="onDataLoaded"
				:readOnly="readOnly">
				</router-view>
		</div>
		<wnl-sidenav-slot class="full-width-sidenav-slot" v-if="!isMainRoute"
			:isVisible="isChatVisible"
			:isDetached="!isChatMounted"
		>
			<wnl-user-about v-if="profile" :profile="profile"></wnl-user-about>
		</wnl-sidenav-slot>
		<div v-if="isChatToggleVisible" class="wnl-chat-toggle" @click="toggleChat">
			<span class="icon is-big">
				<i class="fa fa-chevron-left"></i>
				<span>Pokaż info</span>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.myself-sidenav
		flex: 1

	.wnl-sidenav
		flex: 1
		padding: 7px 0

		&.mobile
			padding: 0

	.mobile-main
		overflow-y: auto

	.full-width-sidenav-slot
		flex-basis: auto

	.wnl-chat-toggle
		padding: 15px

</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	import UserAbout from 'js/components/users/UserAbout.vue'
	import MainNav from 'js/components/MainNav'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import { isProduction, getApiUrl } from 'js/utils/env'
	import QuestionsFilters from 'js/components/questions/QuestionsFilters'

	export default {
		name: 'MainUsers',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-user-about': UserAbout,
		},
		props: ['view'],
		data() {
			return {
				testMode: false,
				profile: {},
				readOnly: true
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isChatMounted', 'isChatVisible', 'isMobileProfile', 'isChatToggleVisible']),
			isProduction() {
				return isProduction()
			},
			isProduction() {
				return isProduction()
			},
			isMainRoute() {
				return this.$route.name === 'all'
			},
			items() {
				let items = [
					{
						text: 'Ziomki',
						itemClass: 'heading small',
					},
					{
						text: 'Wszystkie ziomki',
						itemClass: 'has-icon',
						to: {
							name: 'all',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-address-book',
						iconTitle: 'Wszystkie ziomki',
					}
				]

				return items
			},
		},
		methods: {
			...mapActions(['killChat', 'toggleChat']),
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			},
			onDataLoaded({profile}) {
				return this.profile = profile
			},
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
	}
</script>
