<template>
	<!-- Breadcrumbs -->
	<div class="wnl-sidenav" :class="{ mobile: isMobileNavigation }">
		<!-- Items -->
		<ul class="items" v-if="items">
			<li class="item heading small" v-if="itemsHeading">
				<span class="item-wrapper">
					{{itemsHeading}}
				</span>
			</li>
			<wnl-sidenav-group v-if="isOption('hasGroups')" v-for="(item, index) in items"
				:item="item"
				:key="index"
				:forceGroupOpen="isOption('forceGroupsOpen')"
				:showSubitemsCount="isOption('showSubitemsCount')"
			>
			</wnl-sidenav-group>
			<!-- v-else doesnt cooperate with v-for https://github.com/vuejs/vue/issues/3479 -->
			<wnl-sidenav-item v-if="!isOption('hasGroups')" v-for="(item, index) in items"
				:item="item"
				:key="index"
			>
				{{item.text}}
			</wnl-sidenav-item>
		</ul>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		height: calc($main-height - 2 * $margin-small)

		&.mobile
			height: auto
			width: 100%

			.item
				border-bottom: $border-light-gray

				&:hover
					background: transparent

				&:active
					background: $color-background-lighter-gray

				&.with-border
					padding: 0

				.item-wrapper
					line-height: 26px
					padding: $margin-medium $margin-medium $margin-medium $column-padding

			.subitem
				margin-left: 0

				&.with-border
					padding: 0

				a
					line-height: 26px

				.item-wrapper
					padding: $margin-medium 0 $margin-medium $margin-big

			.heading
				background: $color-background-lighter-gray
				margin: 0

				&:hover
					background: $color-background-lighter-gray

			.wnl-sidenav-group
				margin: 0

	.breadcrumbs
		margin: 19px 0

	.items
		.item
			padding: 0
			transition: background-color $transition-length-base

			&:hover
				background: $color-background-lighter-gray
				transition: background-color $transition-length-base

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

		.heading
			padding: $margin-small 0

			&:hover
				background: transparent

		.small
			font-size: $font-size-minus-1

		.big
			font-size: $font-size-plus-1

		.subitem
			.item-wrapper
				padding: $margin-small $column-padding $margin-small $margin-small + $column-padding

		.disabled
			color: $color-inactive-gray

	.progress
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
import SidenavGroup from 'js/components/global/SidenavGroup';
import SidenavItem from 'js/components/global/SidenavItem';
import { mapGetters } from 'vuex';

export default {
	props: ['breadcrumbs', 'items', 'itemsHeading', 'options'],
	components: {
		'wnl-sidenav-group': SidenavGroup,
		'wnl-sidenav-item': SidenavItem,
	},
	computed: {
		...mapGetters(['isMobileNavigation'])
	},
	methods: {
		isOption(option) {
			return typeof this.options === 'object' && !!this.options[option];
		}
	}
};
</script>
