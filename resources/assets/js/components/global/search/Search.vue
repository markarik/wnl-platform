<template lang="html">
	<div class="wnl-search">
		<span class="icon" @click="showOverlay">
			<i class="fa fa-search"></i>
		</span>

		<div class="wnl-overlay" v-show="active">
			<input type="text"
				placeholder="Szukaj..."
				@input="debounceInput"
				ref="input"
			>
			<wnl-slides-search :phrase="phrase" @resultClicked="goToResult" />
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
	import SlidesSearch from './SlidesSearch'

	export default {
		name: 'Search',
		components: {
			'wnl-slides-search': SlidesSearch
		},
		data() {
			return {
				active: false,
				phrase: ''
			}
		},
		methods: {
			showOverlay() {
				this.active = true
				this.$nextTick(() => this.$refs.input.focus())
			},
			hideOverlay() {
				this.active = false
				this.phrase = ''
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
			},
			debounceInput: _.debounce(function({target: {value}}) {
				this.phrase = value
			}, 500),
			goToResult({context}) {
				this.hideOverlay()

				this.$router.replace({
					name: 'screens',
					params: {
						courseId: context.course.id,
						lessonId: context.lesson.id,
						screenId: context.screen.id,
						slide: context.orderNumber + 1
					}
				})
			}
		},
		mounted() {
			window.addEventListener('keydown', this.keyDown)
		},
		beforeDestroy() {
			window.removeEventListener('keydown', this.keyDown)
		}
	};
</script>
