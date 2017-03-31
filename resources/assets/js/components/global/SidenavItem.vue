<template>
	<li class="item" :class="[itemClass, { disabled: isDisabled }]">
		<span class="icon is-small" v-if="isTodo">
			<i title="W trakcie..." class="fa fa-dot-circle-o" v-if="isInProgress"></i>
			<i title="Zrobione!" class="fa fa-check-circle-o" v-else-if="isComplete"></i>
			<i title="Jeszcze przed Tobą" class="fa fa-circle-o" v-else></i>
		</span>
		<span class="icon is-small" v-if="hasIcon">
			<i :title="iconTitle" class="fa" :class="iconClass"></i>
		</span>
		<router-link v-if="isLink" :to="to" :replace="replace">
			<slot></slot>
		</router-link>
		<span v-else>
			<slot></slot>
		</span>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.has-icon

		.icon
			color: $color-inactive-gray

	.icon.is-small
		font-size: $font-size-minus-1
		margin-top: -1px
		margin-right: $margin-tiny
		vertical-align: middle

	.subitem

		.icon.is-small
			margin-right: 0

		a.is-active
			font-weight: $font-weight-regular
</style>

<script>
	export default {
		name: 'SidenavItem',
		props: ['itemClass', 'to', 'isDisabled', 'method', 'iconClass', 'iconTitle'],
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
