<template>
	<div class="wnl-sidenav-group" :class="{'no-items': !hasSubitems}">
		<div class="wnl-sidenav-item-wrapper">
			<div class="wnl-sidenav-group-toggle" @click="toggleNavigationGroup({groupIndex, isOpen: !isOpen})">
				<wnl-sidenav-item
					:item="item"
					:hasSubitems="hasSubitems"
					:toggleIcon="toggleIcon"
					:isOpen="isOpen"
				>
					{{item.text}}
					<span class="subitems-count" v-if="showSubitemsCount && hasSubitems">({{item.subitems.length}})</span>
				</wnl-sidenav-item>
			</div>
			<ul class="wnl-sidenav-subitems" v-if="canRenderSubitems">
				<wnl-sidenav-item
					v-for="(subitem, index) in item.subitems"
					:item="subitem"
					:key="index"
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
		color: $color-gray
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
	components: {
		'wnl-sidenav-item': SidenavItem
	},
	name: 'SidenavGroup',
	props: ['item', 'forceGroupOpen', 'showSubitemsCount'],
	computed: {
		...mapGetters(['isNavigationGroupExpanded']),
		...mapGetters('course', ['nextLesson']),
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
	methods: {
		...mapActions(['toggleNavigationGroup'])
	},
	mounted() {
		if (this.forceGroupOpen && this.isNavigationGroupExpanded(this.groupIndex) !== false) {
			this.toggleNavigationGroup({groupIndex: this.groupIndex, isOpen: true});
		}
	},
	watch: {
		nextLesson(val) {
			if (!this.item || !this.item.subitems || !val) {
				return;
			}

			const isCurrentlyInProgress = this.item.subitems.some((subitem) => {
				return subitem.to.params && subitem.to.params.lessonId === val.id;
			});

			if (isCurrentlyInProgress) {
				this.toggleNavigationGroup({
					groupIndex: this.groupIndex,
					isOpen: true
				});
			}
		}
	},
};
</script>
