<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div class="scrollable-main-container wnl-main" :class="{'full-width': isMobileProfile, 'mobile-main': isMobileProfile}">
			<router-view
				@userDataLoaded="onDataLoaded"
				:filterByHelp="filterByHelp"
				:filterByLocation="filterByLocation">
			</router-view>
		</div>
		<wnl-sidenav-slot class="full-width-sidenav-slot scrollable-container" v-if="!isMainRoute && (isLargeDesktop || isSmallDesktop)"
			:isVisible="true"
			:isDetached="false"
		>
			<wnl-user-about v-if="profile" :profile="profile"></wnl-user-about>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'


	.full-width-sidenav-slot
		flex-basis: auto

	.myself-sidenav
		flex: 1

	.wnl-sidenav
		flex: 1
		padding: 7px 0

		&.mobile
			padding: 0

	.mobile-main
		overflow-y: auto

	.wnl-chat-toggle
		padding: 15px
		flex-basis: auto

</style>

<script>
import { mapActions, mapGetters } from 'vuex';

import UserAbout from 'js/components/users/UserAbout';
import MainNav from 'js/components/MainNav';
import Sidenav from 'js/components/global/Sidenav';
import SidenavSlot from 'js/components/global/SidenavSlot';
import { isProduction, getApiUrl } from 'js/utils/env';
import QuestionsFilters from 'js/components/questions/QuestionsFilters';

export default {
	name: 'MainUsers',
	components: {
		'wnl-user-about': UserAbout,
		'wnl-main-nav': MainNav,
		'wnl-sidenav': Sidenav,
		'wnl-sidenav-slot': SidenavSlot
	},
	props: ['view'],
	data() {
		return {
			testMode: false,
			profile: null,
			filterByHelp: '',
			filterByLocation: ''
		};
	},
	computed: {
		...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isChatMounted', 'isChatVisible', 'isMobileProfile', 'isChatToggleVisible', 'isLargeDesktop', 'isSmallDesktop']),
		isProduction() {
			return isProduction();
		},
		isMainRoute() {
			return this.$route.name === 'all';
		},
	},
	methods: {
		...mapActions(['killChat', 'toggleChat']),
		helpFilterLoaded(filter) {
			return this.filterByHelp = filter;
		},
		locationFilterLoaded(filter) {
			return this.filterByLocation = filter;
		},
		onDataLoaded({profile}) {
			return this.profile = profile;
		},
	}
};
</script>
