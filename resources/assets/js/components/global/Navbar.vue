<template>
	<nav class="nav has-shadow">
		<div class="columns wnl-nav-container">
			<div class="column wnl-nav-column">
				<div class="wnl-left-content wnl-nav-item">
					<router-link :to="{ name: 'courses', params: { courseId: 1 } }" class="wnl-logo-link">
						<img :src="logoSrc" :alt="logoAlt">
					</router-link>
				</div>
			</div>
			<div class="column is-half wnl-nav-column">

			</div>
			<div class="column wnl-nav-column">
				<div class="nav-menu wnl-nav-item">
					{{ currentUserName }}
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
	import { mapGetters } from 'vuex'

	export default {
		name: 'Navbar',
		computed: {
			...mapGetters([
				'current'
			]),
			currentUserName() {
				return this.current.full_name
			},
			logoSrc() {
				return global.$fn.getImageUrl('wnl-logo.svg')
			},
			logoAlt() {
				return 'Logo Więcej niż LEK'
			}
		}
	}
</script>
