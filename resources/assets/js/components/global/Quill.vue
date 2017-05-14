<template>
	<div class="quill-editor">
		<slot></slot>
	</div>
</template>

<style lang="sass" rel="styl">
	@import "node_modules/quill/dist/quill.snow"
</style>

<script>
	import Quill from 'quill'

	export default {
		name: 'Quill',
		props: {
			options: {
				type: Object,
				default: () => ({})
			},
			autofocus: Boolean
		},
		data () {
			return {
				focused: this.autofocus,
				editor: null
			}
		},
		mounted () {
			this.editor = new Quill(this.$el, this.options)
		},
		watch: {
			focused (val) {
				this.editor[val ? 'focus' : 'blur']()
			}
		}
	}
</script>
