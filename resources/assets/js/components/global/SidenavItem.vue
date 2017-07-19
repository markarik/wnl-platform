<template>
	<li class="item" :class="[itemClass, { disabled: isDisabled }]">
		<router-link
			class="item-wrapper"
			v-if="isLink"
			:class="{'is-active': active, 'is-disabled': isDisabled, 'is-completed': completed}"
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
					<i :title="iconTitle" class="fa" :class="iconClass"></i>
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
					<i :title="iconTitle" class="fa" :class="iconClass"></i>
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

	.item-wrapper
		height: 100%
		width: 100%

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

		&.is-active
			background: $color-background-lighter-gray
			font-weight: $font-weight-regular
			transition: background-color $transition-length-base

	.todo
		a:before
			color: $color-background-gray
			content: '○'
			margin-left: $margin-tiny

		a.is-completed:before
			content: '✓'

	.subitem
		margin-left: $margin-base

		.icon.is-small
			margin-right: 0
</style>

<script>
	import progressStore from '../../services/progressStore';

	export default {
		name: 'SidenavItem',
		props: ['itemClass', 'to', 'isDisabled', 'method', 'iconClass', 'iconTitle', 'completed', 'active', 'meta'],
		computed: {
			isLink() {
				return typeof this.to === 'object' && this.to.hasOwnProperty('name')
			},
			hasProgress() {
				return this.hasClass('with-progress')
			},
			isTodo() {
				return this.hasClass('todo')
			},
			isInProgress() {
				return this.hasClass('in-progress')
			},
			isComplete() {
				return this.hasClass('complete')
			},
			replace() {
				return this.method === 'replace'
			},
			hasIcon() {
				return this.hasClass('has-icon')
			},
			hasMeta() {
				return typeof this.meta !== 'undefined' && this.meta.length > 0
			},
		},
		methods: {
			hasClass(className) {
				return this.itemClass.indexOf(className) > -1
			}
		},
	}
</script>
