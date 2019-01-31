<template>
	<div class="item" :class="[itemClass, { disabled: navigationItem.isDisabled }]">
		<router-link
				class="item-wrapper"
				:class="{'router-link-exact-active': navigationItem.active, 'is-disabled': navigationItem.isDisabled, 'is-completed': navigationItem.completed}"
				:to="to"
		>
			<span class="sidenav-item-content">
				{{navigationItem.text}}
				<span class="sidenav-item-meta" v-if="hasMeta">{{navigationItem.meta}}</span>
			</span>
		</router-link>
		<slot name="children">
		</slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.item-wrapper
		height: 100%
		width: 100%
		user-select: none
		display: flex
		line-height: 1.5em
		padding: 7px 15px
		word-break: break-word
		word-wrap: break-word

	.sidenav-item-meta
		color: $color-background-gray
		font-size: $font-size-minus-3
		line-height: $line-height-plus

	.sidenav-item-content
		margin-left: 5px

	a
		transition: background-color $transition-length-base

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
</style>

<script>
import {STATUS_COMPLETE, STATUS_IN_PROGRESS} from 'js/services/progressStore';

export default {
	name: 'LessonNavigationItem',
	props: {
		navigationItem: {
			type: Object,
			required: true
		},
	},
	computed: {
		itemClass() {
			return this.navigationItem.itemClass;
		},
		to() {
			return this.navigationItem.to;
		},
		hasMeta() {
			return this.navigationItem.meta && this.navigationItem.meta.length > 0;
		},
	},
};
</script>
