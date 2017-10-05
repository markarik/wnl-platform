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
		<div class="wnl-middle wnl-app-layout-main" :class="{'full-width': isMobileProfile, 'mobile-main': isMobileProfile}">
			<router-view v-if="!isMainRoute"></router-view>
			<wnl-all v-else></wnl-all>
		</div>
		<!-- <wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop || isChatVisible"
			:hasChat="true"
		>
			<wnl-questions-filters
				v-show="!testMode"
				:activeFilters="activeFilters"
				:fetchingData="fetchingQuestions || fetchingFilters"
				:filters="filters"
				@activeFiltersChanged="onActiveFiltersChanged"
			/>
		</wnl-sidenav-slot> -->
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

</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	import MainNav from 'js/components/MainNav'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import { isProduction, getApiUrl } from 'js/utils/env'
    import AllUsers from 'js/components/users/AllUsers'
	import QuestionsFilters from 'js/components/questions/QuestionsFilters'

	export default {
		name: 'MainUsers',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-all': AllUsers,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: ['view'],
		data() {
			return {
				param: this.$route.params.userId,
				response: {},
				testMode: false,
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile', 'isChatMounted', 'isLargeDesktop', 'isChatVisible']),
			isProduction() {
				return isProduction()
			},
			isMainRoute() {
				return this.$route.name === 'all-users'
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
							name: 'all-users',
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
			responseCondition() {
				return !_.isEmpty(this.response)
			}
		},
		methods: {
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
		// mounted() {
        //     axios.get(getApiUrl(`users/${this.param}/profile`))
		// 		.then((response) => {
		// 			this.response = response
		// 		})
		// 	.catch(exception => $wnl.logger.capture(exception))
		// },
	}
</script>
