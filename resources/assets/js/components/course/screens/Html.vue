<template>
	<div class="wnl-screen-html content" :class="{'wnl-repetitions': isRepetitions}" v-html="content"></div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-screen-html
		margin: $margin-big 0

	.wnl-repetitions
		ol li:nth-child(5n)
			margin-bottom: $margin-base
</style>

<script>
	import _ from 'lodash'

	export default {
		name: 'Html',
		props: ['screenData'],
		computed: {
			content() {
				return this.screenData.content
			},
			isRepetitions() {
				return this.screenData.name === 'Powtórki'
			}
		},
		methods: {
			wrapEmbedded() {
				let iframes = this.$el.getElementsByClassName('ql-video'),
					wrapperClass = 'ratio-16-9-wrapper'

				if (iframes.length > 0) {
					_.each(iframes, (iframe) => {
						let wrapper = document.createElement('div'),
							parent = iframe.parentNode

						wrapper.className = wrapperClass
						parent.replaceChild(wrapper, iframe)
						wrapper.appendChild(iframe)
					})
				}
			}
		},
		mounted() {
			this.wrapEmbedded()
		}
	}
</script>
