<template>
	<div class="navigation-group" :class="{'no-items': !hasChildren}">
		<div class="navigation-group__toggle" @click="toggleNavigationGroup({groupIndex: item.id, isOpen: !isOpen})">
			<div class="navigation-group__item">
				<span class="icon is-small" v-if="hasChildren">
					<i class="toggle fa fa-angle-down" :class="{'fa-rotate-180': isOpen}"></i>
				</span>
				<span class="sidenav-item-content">
					{{groupItem.text}}
					<span class="navigation-group__count" v-if="hasChildren">({{children.length}})</span>
				</span>
			</div>
		</div>
		<div v-if="canRenderChildren">
			<wnl-lesson-item
				v-for="(subitem) in children"
				:item="subitem"
				:key="subitem.id"
			></wnl-lesson-item>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.navigation-group
		margin-bottom: $margin-base

		&__toggle
			color: $color-darkest-gray
			cursor: pointer
			transition: background-color $transition-length-base
			padding: $margin-small 0

			&:hover
				background-color: $color-background-lighter-gray
				transition: background-color $transition-length-base

		&__count
			color: $color-background-gray
			font-size: $font-size-minus-3

		&__item
			display: flex
			align-items: flex-start
			line-height: $line-height-base
			padding: $margin-small $margin-base
			word-break: break-word
			word-wrap: break-word
			font-size: $font-size-minus-1
			letter-spacing: 1px
			text-transform: uppercase

			.icon
				margin-right: $margin-small

			.toggle
				color: $color-background-gray
				transition: transform $transition-length-base

		&.no-items
			margin-bottom: 0

			.navigation-group__toggle
				color: $color-background-gray
				cursor: default

				&:hover
					background: transparent
</style>

<script>
import { mapState, mapGetters, mapActions } from 'vuex';

import WnlLessonItem from 'js/components/course/navigation/LessonItem';
import navigation from 'js/services/navigation';

export default {
	components: {
		WnlLessonItem
	},
	name: 'GroupItem',
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	computed: {
		...mapState('course', ['structure']),
		...mapGetters(['isNavigationGroupExpanded']),
		groupItem() {
			return navigation.composeItem({text: this.item.model.name, itemClass: 'heading small'});
		},
		canRenderChildren() {
			return this.hasChildren && this.isOpen;
		},
		hasChildren() {
			return this.children.length;
		},
		children() {
			return this.structure.filter(node => node.parent_id === this.item.id);
		},
		isOpen() {
			return !!this.isNavigationGroupExpanded(this.item.id);
		}
	},
	methods: {
		...mapActions(['toggleNavigationGroup'])
	},
};
</script>
