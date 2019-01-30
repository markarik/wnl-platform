<template>
	<div class="wnl-navigation-group" :class="{'no-items': !hasSubitems}">
		<div class="wnl-navigation-group__toggle" @click="toggleNavigationGroup({groupIndex: item.id, isOpen: !isOpen})">
			<div class="wnl-navigation-group__item">
				<span class="icon is-small" v-if="hasSubitems">
					<i class="toggle fa fa-angle-down" :class="{'fa-rotate-180': isOpen}"></i>
				</span>
				<span class="sidenav-item-content">
					{{groupItem.text}}
					<span class="wnl-navigation-group__count" v-if="hasSubitems">({{subitems.length}})</span>
				</span>
			</div>
		</div>
		<div class="wnl-sidenav-subitems" v-if="canRenderSubitems">
			<wnl-lesson-item
				v-for="(subitem) in subitems"
				:item="subitem"
				:key="subitem.id"
			></wnl-lesson-item>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-navigation-group
		margin-bottom: $margin-base

		&__toggle
			color: $color-gray
			cursor: pointer
			transition: background-color $transition-length-base
			padding: 7px 0 12px 0

			&:hover
				background-color: $color-background-lighter-gray
				transition: background-color $transition-length-base

		&__count
			color: $color-background-gray
			font-size: $font-size-minus-3

		&__item
			display: flex
			align-items: center
			line-height: 1.5em
			padding: 7px 15px
			word-break: break-word
			word-wrap: break-word
			font-size: 0.875rem
			letter-spacing: 1px
			text-transform: uppercase

			.toggle
				margin-right: 5px
				color: $color-background-gray
				transition: all $transition-length-base

		&.no-items
			margin-bottom: 0

			.wnl-navigation-group__toggle
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
		...mapState('course', ['newStructure']),
		...mapGetters(['isNavigationGroupExpanded']),
		groupItem() {
			return navigation.composeItem({text: this.item.model.name, itemClass: 'heading small'});
		},
		canRenderSubitems() {
			return this.hasSubitems && this.isOpen;
		},
		hasSubitems() {
			return this.subitems.length;
		},
		subitems() {
			return this.newStructure.filter(node => node.parent_id === this.item.id);
		},
		toggleIcon() {
			return this.isOpen ? 'fa-angle-up' : 'fa-angle-down';
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
