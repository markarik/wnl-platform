<template>
	<div
		class="wnl-sidenav-slot"
		v-if="isVisible"
		v-bind:class="{ 'wnl-sidenav-detached': isDetached}"
	>
		<slot></slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-sidenav-slot
		display: flex

	.wnl-sidenav-detached
		background: $color-white
		bottom: 0
		flex-direction: column
		left: 0
		position: fixed
		right: 0
		top: $navbar-height
		z-index: $z-index-sidenav-slot

		.course-sidenav
			max-width: none
			width: auto
</style>

<script>
	import Breadcrumbs from 'js/components/global/Breadcrumbs'
	import SidenavItem from 'js/components/global/SidenavItem'

	export default {
		props: ['isVisible', 'isDetached']
	}
</script>
