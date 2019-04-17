<template>
	<div class="item todo">
		<router-link
			class="item-wrapper"
			:class="{'is-active': isActive, 'is-completed': isCompleted, 'is-disabled': isDisabled}"
			:to="to"
		>
			<span class="sidenav-item-content">
				<slot name="title"></slot>
				<span v-if="meta" class="sidenav-item-meta">{{meta}}</span>
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

		&.is-disabled
			pointer-events: none
			color: $color-lighter-gray

	.sidenav-item-meta
		color: $color-background-gray
		font-size: $font-size-minus-3
		line-height: $line-height-plus

	.sidenav-item-content
		margin-left: 5px

	a
		transition: background-color $transition-length-base

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
</style>

<script>
export default {
	name: 'LessonNavigationItem',
	props: {
		to: {
			type: [Object, String],
			required: true
		},
		isActive: {
			type: Boolean,
			default: false
		},
		isCompleted: {
			type: Boolean,
			default: false
		},
		isDisabled: {
			type: Boolean,
			default: false
		},
		meta: {
			type: String,
			default: ''
		}
	},
};
</script>
