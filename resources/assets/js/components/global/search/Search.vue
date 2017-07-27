<template lang="html">
	<div class="wnl-search">
		<span class="icon" @click="showOverlay">
			<i class="fa fa-search"></i>
		</span>

		<div class="wnl-overlay" v-if="active">
			<input type="text"
				   ref="must"
				   v-model="phrase"
				   @change="search"
				   placeholder="Szukaj...">

		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-search
		.icon
			&:hover
				cursor: pointer

		.wnl-overlay
			align-items: flex-start
			justify-content: flex-start

		input
			background: none
			border: rgba(255, 255, 255, 0)
			font-family: $font-family-sans-serif
			font-size: 6rem
			margin: $margin-big

			&:focus
				outline: none
</style>

<script>
	export default {
		name: 'Search',
		data() {
			return {
				active: false,
				phrase: '',
			}
		},
		methods: {
			showOverlay() {
				this.active = true
				this.$nextTick(() => {
					this.$refs.must.focus()
				})
			},
			hideOverlay() {
				this.active = false
				this.phrase = ''
			},
			search() {

			},
			keyDown(e) {
				// Ctrl + Alt + f
				if (e.keyCode === 70 && e.ctrlKey && e.altKey) {
					this.showOverlay()
				}
				// Esc
				if (e.keyCode === 27) {
					this.hideOverlay()
				}
			}
		},
		mounted() {
			window.addEventListener('keydown', this.keyDown)
		}
	}
</script>
