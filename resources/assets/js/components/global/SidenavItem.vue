<template>
	<li class="item" :class="[itemClass, { disabled: isDisabled }]">
		<router-link
			v-if="isLink"
			:to="to"
			:replace="replace"
			class="item-wrapper"
			:class="{'is-active': active, 'is-disabled': isDisabled, 'is-completed': completed}"
		>
			<div class="sidenav-icon-wrapper">
				<span class="icon is-small" v-if="isTodo">
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
		</router-link>
		<span v-else class="item-wrapper">
			<div class="sidenav-icon-wrapper">
				<span class="icon is-small" v-if="isTodo">
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

	a.is-active
		font-weight: $font-weight-black

	a.is-completed:after
		content: '✓'
		margin-left: $margin-tiny

	.subitem
		&::after

		.icon.is-small
			margin-right: 0
</style>

<script>
	import progressStore from '../../services/progressStore';

	export default {
		name: 'SidenavItem',
		props: ['itemClass', 'to', 'isDisabled', 'method', 'iconClass', 'iconTitle', 'completed', 'active'],
		computed: {
			isLink() {
				return typeof this.to === 'object' && this.to.hasOwnProperty('name')
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
		},
		methods: {
			hasClass(className) {
				return this.itemClass.indexOf(className) > -1
			}
		},
	}
</script>
