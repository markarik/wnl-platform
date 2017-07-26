<template>
	<div class="wnl-dropdown">
		<div class="activator" :class="{ 'is-active' : isActive }" @click="toggleActive">
			<slot name="activator"></slot>
		</div>
		<transition name="fade">
			<div v-if="isActive" class="box drawer" :class="{'is-mobile': isMobile, 'is-wide': options.isWide}">
				<slot name="content"></slot>
			</div>
		</transition>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.activator
		align-items: center
		height: 100%
		justify-content: center
		position: relative

		&.is-active:before
			content: ""
			position: absolute
			right: calc(50% - 7px)
			bottom: -1px
			width: 0
			height: 0
			border-style: solid
			border-width: 0 10px 10px 10px
			border-color: transparent transparent $color-white transparent
			z-index: 9999

	.wnl-dropdown
		height: 100%
		max-width: $navbar-height
		min-height: 100%
		min-width: $navbar-height
		position: relative
		width: $navbar-height

	.drawer
		border: $border-light-gray
		-webkit-box-shadow: 2px 2px 10px 5px rgba(67,73,90,0.3)
		-moz-box-shadow: 2px 2px 10px 5px rgba(67,73,90,0.3)
		box-shadow: 2px 2px 10px 5px rgba(67,73,90,0.3)
		padding: 0
		position: absolute
		right: 0
		top: 100%
		z-index: 100

		&.is-wide
			max-width: 100vw
			width: 440px

			&.is-mobile
				border-radius: 0
				position: fixed
				top: $navbar-height
</style>

<script>
	import {mapGetters} from 'vuex'

	export default {
		name: 'Dropdown',
		props: {
			options: {
				default() {
					return {
						isWide: false
					}
				},
				type: Object,
			}
		},
		data() {
			return {
				isActive: true,
			}
		},
		computed: {
			...mapGetters(['isMobile'])
		},
		methods: {
			clickHandler({target}) {
				if (!this.$el.contains(target)) {
					this.isActive = false
					this.$emit('toggled', false)
				}
			},
			toggleActive() {
				this.isActive = !this.isActive
				this.$emit('toggled', this.isActive)
			},
		},
		beforeDestroy() {
			document.removeEventListener('click', this.clickHandler)
		},
		mounted() {
			document.addEventListener('click', this.clickHandler)
		},
		watch: {
			'$route' (to, from) {
				this.isActive = false
			},
		},
	}
</script>
