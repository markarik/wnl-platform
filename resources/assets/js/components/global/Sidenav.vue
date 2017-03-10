<template>
	<!-- Breadcrumbs -->
	<div class="wnl-sidenav">
		<ul class="breadcrumbs" v-if="breadcrumbs">
			<wnl-sidenav-item v-for="breadcrumb in breadcrumbs"
				:itemClass="breadcrumb.itemClass"
				:to="breadcrumb.to"
				:isDisabled="breadcrumb.isDisabled"
				:method="breadcrumb.method"
			>
				{{breadcrumb.text}}
			</wnl-sidenav-item>
		</ul>

		<!-- Items -->
		<ul class="items" v-if="items">
			<wnl-sidenav-item v-for="item in items"
				:itemClass="item.itemClass"
				:to="item.to"
				:isDisabled="item.isDisabled"
				:method="item.method"
			>
				{{item.text}}
			</wnl-sidenav-item>
		</ul>
	</div>

</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		max-width: 250px

	.breadcrumbs
		border-bottom: $border-light-gray
		font-size: $font-size-minus-1
		margin-bottom: 10px

		.small
			font-size: $font-size-minus-2

		.big
			font-size: $font-size-base

	.items

		.item
			padding: 5px 0

		.small
			font-size: $font-size-minus-1
			font-weight: $font-weight-bold

		.big
			font-size: $font-size-plus-1

		.subitem
			padding: 5px 0 5px 10px

		.disabled
			color: $color-inactive-gray
</style>

<script>
	import SidenavItem from 'js/components/global/SidenavItem'

	export default {
		props: ['breadcrumbs', 'items'],
		components: {
			'wnl-sidenav-item': SidenavItem,
		}
	}
</script>
