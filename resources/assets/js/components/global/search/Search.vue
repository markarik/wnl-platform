<template lang="html">
	<div class="wnl-search" @click="showOverlay">
		<span class="icon">
			<i class="fa fa-search"></i>
		</span>

		<transition name="fade">
			<div class="wnl-overlay" ref="overlay" :class="{'is-mobile': isMobile}" @click.stop="" v-show="active">
				<div class="search-input">
					<input
						class="input is-loading"
						placeholder="Szukaj..."
						ref="input"
						type="text"
						@input="debounceInput"
					>
					<span class="icon is-large" @click="hideOverlay">
						<i class="fa fa-close"></i>
					</span>
				</div>
				<div class="results">
					<wnl-slides-search :phrase="phrase" @searchComplete="onSearchComplete"/>
				</div>
			</div>
		</transition>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-overlay
		background: $color-white-overlay
		background: linear-gradient(to bottom, $color-white 0%, $color-white-overlay 100%)
		cursor: default
		overflow-y: auto

	.wnl-search
		align-items: center
		display: flex
		height: 100%
		justify-content: center
		width: 100%

		.icon
			color: $color-gray-dimmed

			&:hover
				cursor: pointer

		.wnl-overlay
			align-items: flex-start
			justify-content: flex-start

			&.is-mobile
				.search-input
					.icon
						height: 7vh
						width: 7vh

						i
							font-size: 5vh

				.results
					padding: $margin-huge $margin-base

		.results
			margin: 7vh 0 $margin-base
			padding: $margin-huge * 2 $margin-base

		.search-input
			align-items: center
			background: $color-white
			box-shadow: 0px 10px 40px 10px rgba(255, 255, 255, 0.75)
			display: flex
			justify-content: space-between
			padding: 0 $margin-base
			position: fixed
			top: 0
			width: 100%
			z-index: $z-index-fullscren-close

			.icon
				border-radius: $border-radius-full
				background: $color-inactive-gray
				color: $color-white
				height: 4vh
				width: 4vh
				transition: background-color $transition-length-base

				i
					font-size: 2vh

				&:hover
					background: $color-background-gray
					transition: background-color $transition-length-base

			input
				background: $color-white
				border: 0
				box-shadow: none
				flex: 1 auto
				font-family: $font-family-sans-serif
				font-size: 6vh
				max-width: 80vw
				padding: $margin-small $margin-base $margin-small 0

				&:focus
					outline: none
</style>

<script>
	import {mapGetters} from 'vuex'
	import {scrollToY} from 'js/utils/animations'

	import SlidesSearch from './SlidesSearch'

	export default {
		name: 'Search',
		components: {
			'wnl-slides-search': SlidesSearch
		},
		data() {
			return {
				active: true,
				phrase: ''
			}
		},
		computed: {
			...mapGetters(['isMobile']),
		},
		methods: {
			debounceInput: _.debounce(function({target: {value}}) {
				this.phrase = value
			}, 500),
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
			onSearchComplete() {
				scrollToY(0, 500, this.$refs.overlay)
			},
			showOverlay() {
				this.active = true
				this.$nextTick(() => this.$refs.input.select())
			},
		},
		mounted() {
			window.addEventListener('keydown', this.keyDown)
		},
		beforeDestroy() {
			window.removeEventListener('keydown', this.keyDown)
		}
	};
</script>
