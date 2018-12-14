<template>
	<li class="item" :class="[itemClass, { disabled: item.isDisabled }]">
		<router-link
			class="item-wrapper"
			v-if="isLink"
			:class="{'router-link-exact-active': item.active, 'is-disabled': item.isDisabled, 'is-completed': item.completed}"
			:replace="replace"
			:to="to"
		>
			<div class="sidenav-icon-wrapper">
				<span class="icon is-small" v-if="hasProgress">
					<i title="W trakcie..." class="fa fa-ellipsis-h" v-if="isInProgress"></i>
					<i title="Zrobione!" class="fa fa-check-square-o" v-else-if="isComplete"></i>
					<i title="Jeszcze przed Tobą" class="fa fa-square-o" v-else></i>
				</span>
				<span class="icon is-small" v-if="hasIcon">
					<i :title="item.iconTitle" class="fa" :class="item.iconClass"></i>
				</span>
			</div>
			<span class="sidenav-item-content">
				<slot></slot>
				<span class="sidenav-item-meta" v-if="hasMeta">{{meta}}</span>
			</span>
		</router-link>
		<span v-else class="item-wrapper">
			<div class="sidenav-icon-wrapper">
				<span class="icon is-small" v-if="hasProgress">
					<i title="W trakcie..." class="fa fa-ellipsis-h" v-if="isInProgress"></i>
					<i title="Zrobione!" class="fa fa-check-square-o" v-else-if="isComplete"></i>
					<i title="Jeszcze przed Tobą" class="fa fa-square-o" v-else></i>
				</span>
				<span class="icon is-small" v-if="hasIcon">
					<i :title="item.iconTitle" class="fa" :class="item.iconClass"></i>
				</span>
				<span class="icon is-small" v-if="hasSubitems">
					<i class="toggle fa fa-angle-down" :class="{'fa-rotate-180': isOpen}"></i>
				</span>
			</div>
			<span class="sidenav-item-content">
				<slot></slot>
			</span>
		</span>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

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
export default {
	name: 'SidenavItem',
	props: ['item', 'onClick', 'hasSubitems', 'isOpen'],
	computed: {
		isLink() {
			return typeof this.to === 'object' && this.to.hasOwnProperty('name');
		},
		hasProgress() {
			return this.hasClass('with-progress');
		},
		isTodo() {
			return this.hasClass('todo');
		},
		isInProgress() {
			return this.hasClass('in-progress');
		},
		isComplete() {
			return this.hasClass('complete');
		},
		replace() {
			return this.item.method === 'replace';
		},
		hasIcon() {
			return this.hasClass('has-icon');
		},
		hasMeta() {
			return typeof this.meta !== 'undefined' && this.meta.length > 0;
		},
		itemClass() {
			return this.item.itemClass;
		},
		meta() {
			return this.item.meta;
		},
		to() {
			return this.item.to;
		}
	},
	methods: {
		hasClass(className) {
			return !!this.itemClass && this.itemClass.indexOf(className) > -1;
		}
	},
};
</script>
