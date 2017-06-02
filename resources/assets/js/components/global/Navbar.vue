<template>
	<nav class="wnl-navbar has-shadow">
		<div class="wnl-navbar-item" v-if="canShowSidenavTrigger">
			<a class="wnl-navbar-sidenav-trigger">
				<span class="icon">
					<i class="fa fa-bars"></i>
				</span>
			</a>
		</div>
		<div class="wnl-navbar-item wnl-navbar-branding">
			<a :href="paymentUrl" class="wnl-logo-link">
				<img :src="logoSrc" :alt="logoAlt">
			</a>
			<div class="breadcrumbs" v-if="canShowBreadcrumbsInNavbar">
				<wnl-breadcrumbs></wnl-breadcrumbs>
			</div>
		</div>
		<div class="wnl-navbar-item wnl-navbar-controls" v-if="canShowControlsInNavbar">
			<span class="icon is-big"><i class="fa fa-search"></i></span>
			<span class="icon is-big"><i class="fa fa-comments-o"></i></span>
			<span class="icon is-big"><i class="fa fa-bell"></i></span>
		</div>
		<div class="wnl-navbar-item">
			<wnl-user-dropdown></wnl-user-dropdown>
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
		z-index: $z-index-navbar

	.wnl-navbar-item
		align-items: center
		display: flex
		height: $navbar-height
		min-height: $navbar-height
		padding: 0 $margin-base

	.wnl-navbar-branding
		flex-grow: 1

	.breadcrumbs
		flex-direction: row
		margin-left: $margin-base
		margin-top: 15px

	.wnl-navbar-controls
		.icon
			color: $color-gray-dimmed
			margin-left: $margin-big

			&.is-active
				color: $color-gray

			&.has-notifications
				color: $color-ocean-blue

	.wnl-nav-item
		align-items: center
		display: flex
		height: 100%

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
	import { mapGetters } from 'vuex'
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
				'canShowBreadcrumbsInNavbar',
				'canShowControlsInNavbar'
			]),
			paymentUrl() {
				return 'https://wiecejnizlek.pl'
			},
			logoSrc() {
				return getImageUrl('wnl-logo.svg')
			},
			logoAlt() {
				return 'Logo Więcej niż LEK'
			}
		},
	}
</script>
