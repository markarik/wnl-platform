<template>
	<div class="wnl-search" @click="showOverlay">
		<span class="icon">
			<i class="fa fa-search"></i>
		</span>

		<transition name="fade">
			<div v-show="active"
				class="search-overlay"
				ref="overlay"
				:class="{'is-touch-screen': isTouchScreen}"
				@click.stop="$refs.input.focus()"
			>
				<div class="search-input">
					<div class="control" :class="{'is-loading': loading}">
						<input
							class="input"
							placeholder="Szukaj..."
							ref="input"
							type="text"
							@input="debounceInput"
						>
					</div>
					<span class="close-icon icon is-large" @click="hideOverlay">
						<i class="fa fa-close"></i>
					</span>
				</div>
				<div class="results">
					<wnl-slides-search
						:phrase="phrase"
						@resultClicked="hideOverlay"
						@searchStarted="loading = true"
						@searchComplete="onSearchComplete"
					/>
				</div>
			</div>
		</transition>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$close-icon-size: 40px
	$close-icon-font-size: 28px

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

		.search-overlay
			align-items: flex-start
			background: $color-white-overlay
			background: linear-gradient(to bottom, $color-white 0%, $color-white-overlay 100%)
			bottom: 0
			cursor: default
			overflow-y: auto
			left: 0
			justify-content: flex-start
			position: fixed
			right: 0
			top: 0
			z-index: $z-index-fullscren

			&.is-touch-screen
				.search-input
					.close-icon
						height: $close-icon-size / 1.5
						width: $close-icon-size / 1.5

						i
							font-size: $close-icon-font-size / 1.5

				.control
					max-width: 80vw

				.results
					padding: $margin-huge + $margin-base $margin-base

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

			.close-icon
				border-radius: $border-radius-full
				background: $color-inactive-gray
				color: $color-white
				height: $close-icon-size
				width: $close-icon-size
				transform: rotate(0)
				transition: all $transition-length-base
				z-index: $z-index-fullscren-close

				i
					font-size: $close-icon-font-size

				&:hover
					background: $color-background-gray
					transform: rotate(90deg)
					transition: all $transition-length-base

			.control
				flex: 1 auto
				padding-right: 5vh
				max-width: 700px

				&::after
					top: 38%
					height: 4vh
					right: $margin-base
					width: 4vh

			.input
				background: $color-white
				border: 0
				box-shadow: none
				font-family: $font-family-sans-serif
				font-size: 6vh
				padding: $margin-base $margin-base $margin-base 0
				width: 100%

				&:focus
					outline: none
</style>

<script>
import {mapGetters} from 'vuex';
import {scrollToY} from 'js/utils/animations';

import SlidesSearch from './SlidesSearch';

export default {
	name: 'Search',
	components: {
		'wnl-slides-search': SlidesSearch
	},
	data() {
		return {
			active: false,
			loading: false,
			phrase: '',
		};
	},
	computed: {
		...mapGetters(['isTouchScreen']),
	},
	methods: {
		debounceInput: _.debounce(function({target: {value}}) {
			this.phrase = value;
		}, 500),
		hideOverlay() {
			this.active = false;
			this.phrase = '';
		},
		keyDown(e) {
			// Ctrl + Alt + f
			if (e.keyCode === 70 && e.ctrlKey && e.altKey) {
				this.showOverlay();
			}
			// Esc
			if (e.keyCode === 27) {
				this.hideOverlay();
			}
		},
		onSearchComplete() {
			this.loading = false;
			scrollToY(0, 500, this.$refs.overlay);
		},
		showOverlay() {
			this.active = true;
			this.$nextTick(() => this.$refs.input.select());
		},
	},
	mounted() {
		window.addEventListener('keydown', this.keyDown);
	},
	beforeDestroy() {
		window.removeEventListener('keydown', this.keyDown);
	}
};
</script>
