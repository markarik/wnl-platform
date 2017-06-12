<template>
	<nav class="wnl-navbar has-shadow">
		<div class="wnl-navbar-item wnl-navbar-sidenav-toggle" v-if="canShowSidenavTrigger">
			<a class="wnl-navbar-sidenav-trigger" @click="toggleSidenav">
				<span class="icon">
					<i class="fa" v-bind:class="sidenavIconClass"></i>
				</span>
			</a>
		</div>
		<div class="wnl-navbar-item wnl-navbar-branding">
			<router-link :to="{ name: 'dashboard' }" class="wnl-logo-link">
				<img :src="logoSrc" :alt="logoAlt">
			</router-link>
			<div class="breadcrumbs" v-if="canShowBreadcrumbsInNavbar">
				<wnl-breadcrumbs></wnl-breadcrumbs>
			</div>
		</div>
		<div class="wnl-navbar-item wnl-navbar-controls" v-if="canShowControlsInNavbar">
			<span class="icon is-big"><i class="fa fa-search"></i></span>
			<span class="icon is-big"><i class="fa fa-bell"></i></span>
	</div>
		<div class="wnl-navbar-item wnl-navbar-profile">
			<wnl-user-dropdown></wnl-user-dropdown>
		</div>
		<div class="wnl-navbar-item wnl-navbar-chat-toggle" v-if="canShowChatToggleInNavbar">
			<span class="icon is-big"><i class="fa" v-bind:class="chatIconClass" @click="toggleChat"></i></span>
		</div>
	</nav>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-navbar
		+small-shadow()
		display: flex
		flex: 0 $navbar-height
		padding: 0 1em
		z-index: $z-index-navbar

	.wnl-navbar-item
		align-items: center
		display: flex
		height: $navbar-height
		min-height: $navbar-height
		padding: 0 0.75em

		.icon
			color: $color-gray-dimmed
			cursor: pointer

	.wnl-navbar-branding
		padding-left: 5px
		padding-right: 5px
		flex-grow: 1

	.wnl-navbar-profile
		padding-right: 0

	.breadcrumbs
		flex-direction: row
		margin-left: $margin-base
		margin-top: 15px

	.wnl-navbar-controls
		.icon
			color: $color-gray-dimmed
			cursor: pointer
			margin-left: $margin-big

			&.is-active
				color: $color-gray

			&.has-notifications
				color: $color-ocean-blue

	.wnl-navbar-sidenav-toggle
		padding-left: 0

	.wnl-navbar-chat-toggle
		padding-right: 0

	.wnl-logo-link
		box-sizing: content-box
		display: block
		max-width: 150px
		padding: $margin-base 0

		img
			display: block

	.wnl-right
		height: 100%
</style>

<script>
	import Breadcrumbs from 'js/components/global/Breadcrumbs'
	import Dropdown from 'js/components/user/Dropdown.vue'
	import { mapGetters, mapActions } from 'vuex'
	import { getImageUrl } from 'js/utils/env'

	export default {
		name: 'Navbar',
		components: {
			'wnl-breadcrumbs': Breadcrumbs,
			'wnl-user-dropdown': Dropdown,
		},
		computed: {
			...mapGetters([
				'currentUserFullName',
				'canShowSidenavTrigger',
				'isSidenavOpen',
				'isChatVisible',
				'canShowBreadcrumbsInNavbar',
				'canShowControlsInNavbar',
				'canShowChatToggleInNavbar'
			]),
			paymentUrl() {
				return 'https://wiecejnizlek.pl'
			},
			logoSrc() {
				return getImageUrl('wnl-logo.svg')
			},
			logoAlt() {
				return 'Logo Więcej niż LEK'
			},
			sidenavIconClass() {
				return this.isSidenavOpen ? 'fa-close' : 'fa-bars'
			},
			chatIconClass() {
				return this.isChatVisible ? 'fa-close' : 'fa-comments-o'
			}
		},
		methods: {
			...mapActions(['toggleSidenav', 'toggleChat'])
		}
	}
</script>
