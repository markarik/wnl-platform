<template>
	<div class="wnl-sidenav-group" :class="{'no-items': !hasSubitems}">
		<div class="wnl-sidenav-item-wrapper">
			<div class="wnl-sidenav-group-toggle" @click="toggleNavigationGroup({groupIndex, isOpen: !isOpen})">
				<wnl-sidenav-item
					:item="item"
					:has-subitems="hasSubitems"
					:toggle-icon="toggleIcon"
					:is-open="isOpen"
				>
					{{item.text}}
					<span v-if="showSubitemsCount && hasSubitems" class="subitems-count">({{item.subitems.length}})</span>
				</wnl-sidenav-item>
			</div>
			<ul v-if="canRenderSubitems" class="wnl-sidenav-subitems">
				<wnl-sidenav-item
					v-for="(subitem, index) in item.subitems"
					:key="index"
					:item="subitem"
				>
					{{subitem.text}}
				</wnl-sidenav-item>
			</ul>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-sidenav-group
		margin-bottom: $margin-base

	.wnl-sidenav-group-toggle
		color: $color-darkest-gray
		cursor: pointer
		transition: background-color $transition-length-base

		&:hover
			background-color: $color-background-lighter-gray
			transition: background-color $transition-length-base

		.subitems-count
			color: $color-background-gray
			font-size: $font-size-minus-3

	.wnl-sidenav-group.no-items
		margin-bottom: 0

		.wnl-sidenav-group-toggle
			color: $color-background-gray
			cursor: default

			&:hover
				background: transparent

			.item
				margin: 0
				padding: $margin-tiny 0

</style>

<script>
import SidenavItem from 'js/components/global/SidenavItem';
import { mapGetters, mapActions } from 'vuex';

export default {
	name: 'SidenavGroup',
	components: {
		'wnl-sidenav-item': SidenavItem
	},
	props: ['item', 'forceGroupOpen', 'showSubitemsCount'],
	computed: {
		...mapGetters(['isNavigationGroupExpanded']),
		canRenderSubitems() {
			return this.hasSubitems && this.isOpen;
		},
		hasSubitems() {
			return this.item.subitems && this.item.subitems.length > 0;
		},
		toggleIcon() {
			return this.isOpen ? 'fa-angle-up' : 'fa-angle-down';
		},
		isOpen() {
			return this.isNavigationGroupExpanded(this.groupIndex);
		},
		groupIndex() {
			return `${this.$route.name}/${this.item.text}`;
		}
	},
	mounted() {
		if (this.forceGroupOpen && this.isNavigationGroupExpanded(this.groupIndex) !== false) {
			this.toggleNavigationGroup({ groupIndex: this.groupIndex, isOpen: true });
		}
	},
	methods: {
		...mapActions(['toggleNavigationGroup'])
	},
};
</script>
