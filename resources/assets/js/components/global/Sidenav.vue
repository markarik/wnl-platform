<template>
	<!-- Breadcrumbs -->
	<div class="wnl-sidenav" v-bind:class="{ mobile: isMobileNavigation }">
		<!-- Items -->
		<ul class="items" v-if="items">
			<li class="item heading small" v-if="itemsHeading">{{itemsHeading}}</li>
			<wnl-sidenav-item v-for="(item, index) in items"
				:itemClass="item.itemClass"
				:to="item.to"
				:isDisabled="item.isDisabled"
				:method="item.method"
				:iconClass="item.iconClass"
				:iconTitle="item.iconTitle"
				:key="index"
				:completed="item.completed"
			>
				{{item.text}}
			</wnl-sidenav-item>
		</ul>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		height: $main-height

		&.mobile
			height: auto
			width: 100%

			.item
				border-bottom: 1px solid $color-light-gray
				line-height: 26px
				padding: 10px 15px

				&.with-border
					padding-left: 10px

				a
					line-height: 26px

			.heading
				background: $color-light-gray
				margin-bottom: 0

				&:first-child
					padding-top: 10px

	.breadcrumbs
		margin: 19px 0

	.items
		.item
			padding: $margin-small 0

			&.with-border
				padding: $margin-base 0 $margin-tiny

				&:first-child
					padding: $margin-tiny 0

		.heading
			padding-top: 10px

			&:first-child
				padding-top: 0

		.small
			font-size: $font-size-minus-1

		.big
			font-size: $font-size-plus-1

		.subitem
			padding: 5px 0 5px 15px

		.disabled
			color: $color-inactive-gray

	.todo
		color: $primary

		&.complete,
		&.complete a
			color: mix($color-green, $color-white, 70%)

		&.in-progress
			font-weight: $font-weight-black

		.icon
			vertical-align: baseline


	.is-active
		font-weight: $font-weight-bold
</style>

<script>
	import Breadcrumbs from 'js/components/global/Breadcrumbs'
	import SidenavItem from 'js/components/global/SidenavItem'
	import { mapGetters } from 'vuex'

	export default {
		props: ['breadcrumbs', 'items', 'itemsHeading'],
		components: {
			'wnl-breadcrumbs': Breadcrumbs,
			'wnl-sidenav-item': SidenavItem,
		},
		computed: {
			...mapGetters(['isMobileNavigation'])
		}
	}
</script>
