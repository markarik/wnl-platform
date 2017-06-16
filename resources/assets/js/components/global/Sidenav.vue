<template>
	<!-- Breadcrumbs -->
	<div class="wnl-sidenav" v-bind:class="{ mobile: isMobileNavigation }">
		<!-- Items -->
		<ul class="items" v-if="items">
			<li class="item heading small" v-if="itemsHeading">
				<span class="item-wrapper">
					{{itemsHeading}}
				</span>
			</li>
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

				&.with-border
					padding: 0

				.item-wrapper
					line-height: 26px
					padding: $margin-medium $margin-medium $margin-medium $column-padding

			.subitem
				.item-wrapper
					padding: $margin-medium 0 $margin-medium $margin-medium + $column-padding

				&.with-border
					padding: 0

				a
					line-height: 26px

			.heading
				background: $color-light-gray
				margin-bottom: 0

	.breadcrumbs
		margin: 19px 0

	.items
		.item
			padding: 0

			.item-wrapper
				display: flex
				line-height: 1.5em
				padding: $margin-small $column-padding
				word-break: break-word
				word-wrap: break-word

			&.with-border
				padding: $margin-base 0 $margin-tiny

				&:first-child
					padding: $margin-tiny 0

		a.item-wrapper
			&:hover
				background: $color-background-lighter-gray

		.heading
			padding-top: 10px

			&:first-child
				padding-top: 0

		.small
			font-size: $font-size-minus-1

		.big
			font-size: $font-size-plus-1

		.subitem
			.item-wrapper
				padding: $margin-small $column-padding $margin-small $margin-small + $column-padding

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
