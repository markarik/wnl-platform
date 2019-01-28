<template>
	<div class="wnl-sidenav-group" :class="{'no-items': !hasSubitems}">
		<div class="wnl-sidenav-item-wrapper">
			<div class="wnl-sidenav-group-toggle" @click="isOpen = !isOpen">
				<span class="item-wrapper">
					<div class="sidenav-icon-wrapper">
						<span class="icon is-small" v-if="hasSubitems">
							<i class="toggle fa fa-angle-down" :class="{'fa-rotate-180': isOpen}"></i>
						</span>
					</div>
					<span class="sidenav-item-content">
						{{groupItem.text}}
						<span class="subitems-count" v-if="hasSubitems">({{subitems.length}})</span>
					</span>
				</span>
			</div>
			<ul class="wnl-sidenav-subitems" v-if="canRenderSubitems">
				<wnl-lesson-item
					v-for="(subitem) in subitems"
					:item="subitem"
					:key="subitem.id"
				></wnl-lesson-item>
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

	=subitem-indent($nestLevel)
		margin-left: $margin-base + $margin-base * $nestLevel

	.item-wrapper
		height: 100%
		width: 100%
		user-select: none

	.is-grouped
		padding-left: $margin-base

	.has-icon
		.icon
			color: $color-inactive-gray

	.icon.is-small
		font-size: $font-size-minus-1
		margin-top: -1px
		margin-right: $margin-tiny

	.sidenav-icon-wrapper
		margin-right: 5px

		.icon
			margin-top: 0

	.sidenav-item-meta
		color: $color-background-gray
		font-size: $font-size-minus-3
		line-height: $line-height-plus

	a
		transition: background-color $transition-length-base

		&:hover
			color: $color-ocean-blue

		&.router-link-exact-active
			background: $color-background-lighter-gray
			font-weight: $font-weight-regular
			transition: background-color $transition-length-base

		&.is-active
			font-weight: $font-weight-regular

	.todo
		a:before
			color: $color-background-gray
			content: '○'
			margin-left: $margin-tiny

		a.is-completed:before
			content: '✓'

	.subitem
		+subitem-indent(0)

		.icon.is-small
			margin-right: 0

	.subitem--second
		+subitem-indent(2)

	.toggle
		color: $color-background-gray
		transition: all $transition-length-base

</style>

<script>
import WnlLessonItem from 'js/components/course/navigation/LessonItem';
import { mapState } from 'vuex';
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
	data() {
		return {
			isOpen: false
		};
	},
	computed: {
		...mapState('course', ['newStructure']),
		groupItem() {
			return navigation.composeItem({text: this.item.model.name, itemClass: 'heading small'});
		},
		canRenderSubitems() {
			return this.hasSubitems && this.isOpen;
		},
		hasSubitems() {
			return this.subitems && this. subitems.length > 0;
		},
		subitems() {
			return this.newStructure.filter(node => node.parent_id === this.item.id);
		},
		toggleIcon() {
			return this.isOpen ? 'fa-angle-up' : 'fa-angle-down';
		},
	},
};
</script>
