<template>
	<nav class="nav has-shadow">
		<div class="columns wnl-nav-container">
			<div class="column wnl-nav-column">
				<div class="wnl-left-content wnl-nav-item">
					<router-link :to="{ name: 'dashboard' }" class="wnl-logo-link">
						<img :src="logoSrc" :alt="logoAlt">
					</router-link>
				</div>
			</div>
			<div class="column is-half wnl-nav-column">

			</div>
			<div class="column wnl-nav-column">
				<div class="nav-menu wnl-nav-item">
					<wnl-user-dropdown></wnl-user-dropdown>
				</div>
			</div>
		</div>
	</nav>
</template>

<style lang="sass">
	.wnl-nav-column
		height: 100%

	.wnl-nav-item
		align-items: center
		display: flex
		height: 100%

	.wnl-nav-container
		width: 100%

	.wnl-logo-link
		display: block
		max-width: 175px

		img
			display: block
</style>

<script>
	import Dropdown from 'js/components/user/Dropdown.vue'
	import { mapGetters } from 'vuex'
	import { getImageUrl } from 'js/utils/env'

	export default {
		name: 'Navbar',
		computed: {
			...mapGetters([
				'currentUserFullName'
			]),
			logoSrc() {
				return getImageUrl('wnl-logo.svg')
			},
			logoAlt() {
				return 'Logo Więcej niż LEK'
			}
		},
		components: {
			'wnl-user-dropdown': Dropdown,
		},
	}
</script>
